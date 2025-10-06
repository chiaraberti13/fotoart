<?php
/**
 * FotoArt Puzzle - Upload Controller
 * Handles image uploads from the customization wizard
 */

class FotoartpuzzleUploadModuleFrontController extends ModuleFrontController
{
    /**
     * @var FAPImageProcessor
     */
    protected $fileProcessor;

    /**
     * @var FAPLogger
     */
    protected $logger;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
        $this->fileProcessor = new FAPImageProcessor();
        $this->logger = FAPLogger::create();
    }

    /**
     * Initialize content
     */
    public function initContent()
    {
        parent::initContent();
        
        header('Content-Type: application/json; charset=utf-8');
        
        $response = $this->handleRequest();
        
        echo json_encode($response);
        exit;
    }

    /**
     * Handle upload request
     *
     * @return array
     */
    private function handleRequest()
    {
        try {
            $this->validateToken();
            $this->validateRequestMethod();
            
            $file = $this->getUploadedFile();
            $this->validateFileUpload($file);
            $this->validateFileSize($file);
            $this->validateFileExtension($file);
            $this->validateMimeType($file);
            $this->validateImageDimensions($file);
            
            $tempPath = $this->moveToTemporary($file);

            $this->logger->info('Image uploaded successfully', [
                'file' => basename($tempPath),
                'size' => $file['size'],
                'cart_id' => $this->context->cart ? $this->context->cart->id : 0,
            ]);

            return [
                'success' => true,
                'file' => $tempPath,
                'download_url' => $this->module->getDownloadLink($tempPath, 'front', ['disposition' => 'inline']),
            ];
        } catch (Exception $exception) {
            $this->logger->error('Upload error', [
                'error' => $exception->getMessage(),
                'file' => isset($_FILES['file']) ? $_FILES['file']['name'] : 'none',
                'cart_id' => $this->context->cart ? $this->context->cart->id : 0,
            ]);

            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Validate security token
     *
     * @throws Exception
     */
    private function validateToken()
    {
        $token = Tools::getValue('token');
        $expectedToken = $this->module->getFrontToken('upload');
        
        if (!$token || $token !== $expectedToken) {
            $this->logger->warning('Invalid upload token', [
                'received' => $token ? 'present' : 'missing',
                'ip' => Tools::getRemoteAddr(),
            ]);
            throw new Exception($this->module->l('Invalid security token.', 'upload'));
        }
    }

    /**
     * Validate request method
     *
     * @throws Exception
     */
    private function validateRequestMethod()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception($this->module->l('Invalid request method.', 'upload'));
        }
    }

    /**
     * Get uploaded file from request
     *
     * @return array
     * @throws Exception
     */
    private function getUploadedFile()
    {
        if (empty($_FILES['file'])) {
            throw new Exception($this->module->l('No file uploaded.', 'upload'));
        }

        $file = $_FILES['file'];

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception($this->module->l('File upload failed.', 'upload'));
        }

        return $file;
    }

    /**
     * Validate file upload status
     *
     * @param array $file
     * @throws Exception
     */
    private function validateFileUpload(array $file)
    {
        if (!isset($file['error'])) {
            throw new Exception($this->module->l('File upload error.', 'upload'));
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                return;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception($this->module->l('The file is too large.', 'upload'));
            case UPLOAD_ERR_PARTIAL:
                throw new Exception($this->module->l('The file was only partially uploaded.', 'upload'));
            case UPLOAD_ERR_NO_FILE:
                throw new Exception($this->module->l('No file was uploaded.', 'upload'));
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->logger->error('Upload failed: no temp directory');
                throw new Exception($this->module->l('Server configuration error.', 'upload'));
            case UPLOAD_ERR_CANT_WRITE:
                $this->logger->error('Upload failed: cannot write to disk');
                throw new Exception($this->module->l('Server configuration error.', 'upload'));
            case UPLOAD_ERR_EXTENSION:
                $this->logger->error('Upload failed: PHP extension stopped upload');
                throw new Exception($this->module->l('Upload blocked by server.', 'upload'));
            default:
                throw new Exception($this->module->l('Unknown upload error.', 'upload'));
        }
    }

    /**
     * Validate file size
     *
     * @param array $file
     * @throws Exception
     */
    private function validateFileSize(array $file)
    {
        if (!isset($file['size']) || $file['size'] <= 0) {
            throw new Exception($this->module->l('Invalid file size.', 'upload'));
        }

        $maxMb = (int) Configuration::get(FAPConfiguration::MAX_UPLOAD_SIZE);
        $maxBytes = $maxMb * 1024 * 1024;

        if ($file['size'] > $maxBytes) {
            throw new Exception(
                sprintf(
                    $this->module->l('File exceeds the maximum allowed size of %d MB.', 'upload'),
                    $maxMb
                )
            );
        }
    }

    /**
     * Validate file extension
     *
     * @param array $file
     * @throws Exception
     */
    private function validateFileExtension(array $file)
    {
        if (!isset($file['name'])) {
            throw new Exception($this->module->l('Invalid file name.', 'upload'));
        }

        $allowedExtensions = array_map(
            'strtolower',
            array_map(
                'trim',
                explode(',', (string) Configuration::get(FAPConfiguration::ALLOWED_EXTENSIONS))
            )
        );

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new Exception(
                sprintf(
                    $this->module->l('File extension not allowed. Allowed: %s', 'upload'),
                    implode(', ', array_map('strtoupper', $allowedExtensions))
                )
            );
        }
    }

    /**
     * Validate MIME type with multiple methods
     *
     * @param array $file
     * @throws Exception
     */
    private function validateMimeType(array $file)
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
        ];

        $detectedMimes = [];

        // Method 1: finfo (most reliable)
        if (function_exists('finfo_open') && file_exists($file['tmp_name'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = finfo_file($finfo, $file['tmp_name']);
                if ($mime) {
                    $detectedMimes['finfo'] = $mime;
                }
                finfo_close($finfo);
            }
        }

        // Method 2: mime_content_type
        if (function_exists('mime_content_type') && file_exists($file['tmp_name'])) {
            $mime = mime_content_type($file['tmp_name']);
            if ($mime) {
                $detectedMimes['mime_content_type'] = $mime;
            }
        }

        // Method 3: getimagesize (specific for images)
        if (file_exists($file['tmp_name'])) {
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo && isset($imageInfo['mime'])) {
                $detectedMimes['getimagesize'] = $imageInfo['mime'];
            }
        }

        // Method 4: Fallback to uploaded MIME (least reliable)
        if (isset($file['type']) && $file['type']) {
            $detectedMimes['uploaded'] = $file['type'];
        }

        // Log detected MIME types
        $this->logger->debug('MIME type detection', [
            'file' => $file['name'],
            'detected' => $detectedMimes,
        ]);

        if (empty($detectedMimes)) {
            throw new Exception($this->module->l('Unable to determine file type.', 'upload'));
        }

        // Check if at least one detection method found a valid MIME type
        $validMimeFound = false;
        foreach ($detectedMimes as $method => $mime) {
            if (in_array(strtolower($mime), $allowedMimeTypes, true)) {
                $validMimeFound = true;
                break;
            }
        }

        if (!$validMimeFound) {
            throw new Exception(
                $this->module->l('Invalid file type. Only JPEG and PNG images are allowed.', 'upload')
            );
        }

        // Additional security check: verify it's actually an image
        if (!@getimagesize($file['tmp_name'])) {
            throw new Exception($this->module->l('The uploaded file is not a valid image.', 'upload'));
        }
    }

    /**
     * Validate image dimensions
     *
     * @param array $file
     * @throws Exception
     */
    private function validateImageDimensions(array $file)
    {
        if (!file_exists($file['tmp_name'])) {
            throw new Exception($this->module->l('Uploaded file not found.', 'upload'));
        }

        $imageSize = @getimagesize($file['tmp_name']);
        if (!$imageSize) {
            throw new Exception($this->module->l('Unable to read image dimensions.', 'upload'));
        }

        $minWidth = (int) Configuration::get(FAPConfiguration::MIN_WIDTH);
        $minHeight = (int) Configuration::get(FAPConfiguration::MIN_HEIGHT);

        if ($imageSize[0] < $minWidth || $imageSize[1] < $minHeight) {
            throw new Exception(
                sprintf(
                    $this->module->l('Image dimensions are too small. Minimum required: %dx%d pixels. Your image: %dx%d pixels.', 'upload'),
                    $minWidth,
                    $minHeight,
                    $imageSize[0],
                    $imageSize[1]
                )
            );
        }

        // Log image info
        $this->logger->debug('Image validated', [
            'file' => $file['name'],
            'dimensions' => $imageSize[0] . 'x' . $imageSize[1],
            'mime' => $imageSize['mime'],
        ]);
    }

    /**
     * Move uploaded file to temporary storage and process
     *
     * @param array $file
     * @return string Processed file path
     * @throws Exception
     */
    private function moveToTemporary(array $file)
    {
        $cart = $this->context->cart;
        if (!$cart || !$cart->id) {
            throw new Exception($this->module->l('Cart not available.', 'upload'));
        }

        $tempDir = FAPPathBuilder::getCartPath((int) $cart->id);
        if (!is_dir($tempDir)) {
            if (!@mkdir($tempDir, 0750, true)) {
                $this->logger->error('Failed to create temp directory', [
                    'path' => $tempDir,
                ]);
                throw new Exception($this->module->l('Unable to create temporary directory.', 'upload'));
            }
        }

        // Generate secure filename
        $basename = Tools::passwdGen(16);
        if ((bool) Configuration::get(FAPConfiguration::ANONYMIZE_FILENAMES)) {
            $basename = hash('sha256', $basename . microtime(true) . $file['name']);
        } else {
            // Keep original name but sanitize
            $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
            $originalName = Tools::str2url($originalName);
            $basename = $originalName . '_' . Tools::passwdGen(8);
        }

        $destination = rtrim($tempDir, '/\\') . '/' . $basename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->logger->error('Failed to move uploaded file', [
                'from' => $file['tmp_name'],
                'to' => $destination,
            ]);
            throw new Exception($this->module->l('Unable to save uploaded file.', 'upload'));
        }

        // Process image (re-encode, strip EXIF, normalize)
        try {
            $processed = $this->fileProcessor->process($destination, $destination . '_processed');
            
            // Remove original
            @unlink($destination);

            return $processed['path'];
        } catch (Exception $e) {
            // Clean up on error
            @unlink($destination);
            
            $this->logger->error('Image processing failed', [
                'file' => $file['name'],
                'error' => $e->getMessage(),
            ]);
            
            throw new Exception($this->module->l('Image processing failed.', 'upload'));
        }
    }
}