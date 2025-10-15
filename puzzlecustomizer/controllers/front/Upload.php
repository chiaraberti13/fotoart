<?php
/**
 * Gestione caricamento immagini via AJAX.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__DIR__, 2) . '/classes/ImageProcessor.php';
require_once dirname(__DIR__, 2) . '/classes/PuzzleImageFormat.php';

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
            PrestaShopLogger::addLog(
                'Puzzle Customizer Upload Error: ' . $e->getMessage(),
                2,
                null,
                'PuzzleCustomizer',
                null,
                true
            );

            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Puzzle Customizer Unexpected Error: ' . $e->getMessage() . ' | ' . $e->getTraceAsString(),
                3,
                null,
                'PuzzleCustomizer',
                null,
                true
            );

            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $this->module->l('Unexpected error during upload. Please try again.'),
            ]));
        }
    }

    protected function processUpload()
    {
        $this->checkRateLimit();

        if (!Tools::isSubmit('ajax')) {
            throw new PuzzleImageProcessorException($this->module->l('Invalid request.'));
        }

        if (!Tools::getToken(false)) {
            throw new PuzzleImageProcessorException($this->module->l('Missing security token.'));
        }

        $token = Tools::getValue('token');
        if (!$token || $token !== Tools::getToken(false)) {
            throw new PuzzleImageProcessorException($this->module->l('Security token validation failed.'));
        }

        if (!isset($_FILES['file'])) {
            throw new PuzzleImageProcessorException($this->module->l('Nessun file selezionato.'));
        }

        if (!isset($_FILES['file']['size']) || (int) $_FILES['file']['size'] <= 0) {
            throw new PuzzleImageProcessorException($this->module->l('Empty file.'));
        }

        $maxSize = (int) Configuration::get('PUZZLE_MAX_FILESIZE', 50) * 1024 * 1024;
        if ((int) $_FILES['file']['size'] > $maxSize) {
            throw new PuzzleImageProcessorException(
                sprintf($this->module->l('File too large. Maximum size: %d MB'), $maxSize / 1024 / 1024)
            );
        }

        $processor = new ImageProcessor();
        $collection = new PrestaShopCollection('PuzzleImageFormat');
        $allowedFormats = [];

        foreach ($collection as $item) {
            $allowedFormats[] = $item->getFields();
        }

        $processor->validateUpload($_FILES['file'], $allowedFormats);

        $imageInfo = getimagesize($_FILES['file']['tmp_name']);
        if (!$imageInfo) {
            throw new PuzzleImageProcessorException($this->module->l('Invalid image file.'));
        }

        $width = (int) $imageInfo[0];
        $height = (int) $imageInfo[1];

        $minWidth = (int) Configuration::get('PUZZLE_MIN_IMAGE_WIDTH', 1000);
        $minHeight = (int) Configuration::get('PUZZLE_MIN_IMAGE_HEIGHT', 1000);

        $warnings = [];

        if ($width < $minWidth || $height < $minHeight) {
            $warnings[] = sprintf(
                $this->module->l('Image resolution is low (%dx%d pixels). Minimum recommended: %dx%d pixels. Print quality may be affected.'),
                $width,
                $height,
                $minWidth,
                $minHeight
            );
        }

        $dpiValidation = $processor->validateDPI($_FILES['file']['tmp_name'], 300);
        if (!$dpiValidation['valid']) {
            $warnings[] = $dpiValidation['message'];
        }

        $token = Tools::passwdGen(32);
        $tempDir = _PS_MODULE_DIR_ . $this->module->name . '/uploads/temp/';
        $originalFilename = preg_replace('/[^a-z0-9\._-]+/i', '-', $_FILES['file']['name']);
        $filename = $token . '_' . $originalFilename;
        $destination = $tempDir . $filename;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            throw new PuzzleImageProcessorException($this->module->l('Impossibile spostare il file caricato.'));
        }

        $extension = Tools::strtolower(pathinfo($destination, PATHINFO_EXTENSION));
        $needsConversion = in_array($extension, ['heic', 'heif', 'bmp', 'tiff', 'tif']);

        if ($needsConversion) {
            try {
                $convertedPath = $processor->autoConvert($destination, $allowedFormats);
                $destination = $convertedPath;
                $filename = basename($convertedPath);
            } catch (Exception $e) {
                @unlink($destination);
                throw new PuzzleImageProcessorException(
                    $this->module->l('Image conversion failed: ') . $e->getMessage()
                );
            }
        }

        $thumbnailPath = $tempDir . 'thumb_' . $filename;
        try {
            $processor->createThumbnail($destination, $thumbnailPath, 800, 600);
        } catch (Exception $e) {
            // Thumbnail creation failure should not block upload
        }

        $response = [
            'success' => true,
            'token' => $token,
            'file' => $filename,
            'warnings' => $warnings,
            'image_info' => [
                'width' => $width,
                'height' => $height,
                'dpi' => $dpiValidation['actual_dpi'],
            ],
        ];

        $this->ajaxDie(json_encode($response));
    }

    /**
     * Simple rate limiting to avoid abuse.
     *
     * @throws PuzzleImageProcessorException
     */
    protected function checkRateLimit()
    {
        $ip = Tools::getRemoteAddr();
        $cacheKey = 'puzzle_upload_' . md5($ip);

        $rateInfo = Cache::retrieve($cacheKey);
        $now = time();

        if (!is_array($rateInfo) || $rateInfo['expires'] < $now) {
            $rateInfo = ['count' => 0, 'expires' => $now + 60];
        }

        if ($rateInfo['count'] >= 10) {
            throw new PuzzleImageProcessorException(
                $this->module->l('Upload rate limit exceeded. Please try again later.')
            );
        }

        $rateInfo['count']++;
        Cache::store($cacheKey, $rateInfo);
    }
}
