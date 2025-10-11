<?php

require_once _PS_MODULE_DIR_ . 'art_puzzle/autoload.php';

use ArtPuzzle\ArtPuzzleLogger;
use ArtPuzzle\PuzzleImageProcessor;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class ArtPuzzleUploadModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (Tools::isSubmit('submitImageUpload') && isset($_FILES['puzzle_image'])) {
            $file = $_FILES['puzzle_image'];
            $maxSizeMb = (int) Configuration::get('ART_PUZZLE_MAX_UPLOAD_SIZE', 10);
            $allowedTypes = ['jpg', 'jpeg', 'png'];

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileSize = (int) $file['size'];

            // Validazione estensione
            if (!in_array($extension, $allowedTypes)) {
                $this->logAndError('Formato immagine non supportato: ' . $extension);
                return;
            }

            // Validazione dimensione
            if ($fileSize > $maxSizeMb * 1024 * 1024) {
                $this->logAndError('File troppo grande: ' . $fileSize . ' bytes');
                return;
            }

            // Directory di upload
            $uploadDir = _PS_MODULE_DIR_ . 'art_puzzle/upload/';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                $this->logAndError('Impossibile creare la directory di upload');
                return;
            }

            $filename = uniqid('puzzle_') . '.' . $extension;
            $targetPath = $uploadDir . $filename;

            // Spostamento file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                $this->logAndError('Errore durante il salvataggio del file.');
                return;
            }

            // Crop immagine
            $processor = new PuzzleImageProcessor();
            $croppedPath = $processor->cropImage($targetPath);

            if (!$croppedPath || !file_exists($croppedPath)) {
                $this->logAndError('Errore nel crop dell\'immagine');
                return;
            }

            // Salvataggio in sessione
            $this->context->cookie->__set('art_puzzle_uploaded_image', basename($croppedPath));

            Tools::redirect($this->context->link->getModuleLink('art_puzzle', 'format'));
        }
    }

    private function logAndError($message)
    {
        $this->errors[] = $this->module->l($message);
        if (class_exists(ArtPuzzleLogger::class)) {
            ArtPuzzleLogger::log('[UPLOAD] ' . $message);
        }
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            'upload_action' => $this->context->link->getModuleLink('art_puzzle', 'upload'),
            'max_upload_size' => Configuration::get('ART_PUZZLE_MAX_UPLOAD_SIZE', 10),
        ]);

        $this->setTemplate('module:art_puzzle/views/templates/front/customizer.tpl');
    }
}
