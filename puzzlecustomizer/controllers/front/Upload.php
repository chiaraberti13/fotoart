<?php
/**
 * Gestione caricamento immagini via AJAX.
 */

class PuzzlecustomizerUploadModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        header('Content-Type: application/json');

        try {
            $this->processUpload();
        } catch (PuzzleImageProcessorException $e) {
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
        } catch (Exception $e) {
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $this->module->l('Errore inatteso durante il caricamento.'),
            ]));
        }
    }

    protected function processUpload()
    {
        if (!Tools::isSubmit('ajax')) {
            throw new PuzzleImageProcessorException($this->module->l('Richiesta non valida.'));
        }

        if (!isset($_FILES['file'])) {
            throw new PuzzleImageProcessorException($this->module->l('Nessun file selezionato.'));
        }

        $processor = new ImageProcessor();
        $collection = new PrestaShopCollection('PuzzleImageFormat');
        $allowedFormats = [];

        foreach ($collection as $item) {
            $allowedFormats[] = $item->getFields();
        }

        $processor->validateUpload($_FILES['file'], $allowedFormats);

        $token = Tools::passwdGen(32);
        $tempDir = _PS_MODULE_DIR_ . $this->module->name . '/uploads/temp/';
        $filename = $token . '_' . preg_replace('/[^a-z0-9\._-]+/i', '-', $_FILES['file']['name']);
        $destination = $tempDir . $filename;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            throw new PuzzleImageProcessorException($this->module->l('Impossibile spostare il file caricato.'));
        }

        $this->ajaxDie(json_encode([
            'success' => true,
            'token' => $token,
            'file' => $filename,
        ]));
    }
}
