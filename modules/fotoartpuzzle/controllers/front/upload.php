<?php

class FotoartpuzzleUploadModuleFrontController extends ModuleFrontController
{
    protected $fileProcessor;
    protected $logger;

    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
        $this->fileProcessor = new FAPImageProcessor();
        $this->logger = FAPLogger::create();
    }

    public function initContent()
    {
        parent::initContent();
        $this->ajaxDie(json_encode($this->handleRequest()));
    }

    private function handleRequest()
    {
        try {
            $this->validateToken();
            $file = $this->getUploadedFile();
            $tempPath = $this->moveToTemporary($file);

            return [
                'success' => true,
                'file' => $tempPath,
                'download_url' => $this->module->getDownloadLink($tempPath, 'front', ['disposition' => 'inline']),
            ];
        } catch (Exception $exception) {
            $this->logger->error('Upload error', ['error' => $exception->getMessage()]);

            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    private function validateToken()
    {
        $token = Tools::getValue('token');
        if (!$token || $token !== $this->module->getFrontToken('upload')) {
            throw new Exception($this->module->l('Invalid token.'));
        }
    }

    private function getUploadedFile()
    {
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception($this->module->l('No file uploaded.'));
        }

        $file = $_FILES['file'];
        $maxMb = (int) Configuration::get(FAPConfiguration::MAX_UPLOAD_SIZE);
        if (($file['size'] / 1024 / 1024) > $maxMb) {
            throw new Exception($this->module->l('File exceeds the allowed size.'));
        }

        $allowed = array_map('strtolower', array_map('trim', explode(',', (string) Configuration::get(FAPConfiguration::ALLOWED_EXTENSIONS))));
        $extension = Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed, true)) {
            throw new Exception($this->module->l('File extension not allowed.'));
        }

        return $file;
    }

    private function moveToTemporary(array $file)
    {
        $cart = $this->context->cart;
        if (!$cart || !$cart->id) {
            throw new Exception($this->module->l('Cart not available.'));
        }

        $tempDir = FAPPathBuilder::getCartPath((int) $cart->id);
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0750, true);
        }

        $basename = Tools::passwdGen(12);
        if ((bool) Configuration::get(FAPConfiguration::ANONYMIZE_FILENAMES)) {
            $basename = sha1($basename . microtime(true));
        }

        $destination = $tempDir . '/' . $basename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception($this->module->l('Unable to move uploaded file.'));
        }

        $processed = $this->fileProcessor->process($destination, $destination . '_processed');
        @unlink($destination);

        $this->assertMinimumDimensions($processed);

        return $processed['path'];
    }

    private function assertMinimumDimensions(array $imageData)
    {
        $minWidth = (int) Configuration::get(FAPConfiguration::MIN_WIDTH);
        $minHeight = (int) Configuration::get(FAPConfiguration::MIN_HEIGHT);

        if ((!$minWidth && !$minHeight) || empty($imageData['width']) || empty($imageData['height'])) {
            return;
        }

        $width = (int) $imageData['width'];
        $height = (int) $imageData['height'];

        if (($minWidth && $width < $minWidth) || ($minHeight && $height < $minHeight)) {
            if (!empty($imageData['path']) && file_exists($imageData['path'])) {
                @unlink($imageData['path']);
            }

            $requirements = trim(
                ($minWidth ? $minWidth . 'px' : '')
                . ($minWidth && $minHeight ? ' × ' : '')
                . ($minHeight ? $minHeight . 'px' : '')
            );

            throw new Exception(sprintf(
                $this->module->l('Uploaded image is too small. Minimum size is %s.'),
                $requirements
            ));
        }
    }
}
