<?php
/**
 * Gestione elaborazione immagini caricati dagli utenti.
 */

require_once __DIR__ . '/PuzzleCustomization.php';

class PuzzleImageProcessorException extends Exception
{
}

class ImageProcessor
{
    /**
     * Valida il file caricato.
     *
     * @throws PuzzleImageProcessorException
     */
    public function validateUpload(array $file, array $allowedFormats)
    {
        if (!isset($file['tmp_name']) || !is_file($file['tmp_name'])) {
            throw new PuzzleImageProcessorException('File non valido.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeFromContent = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $mime = mime_content_type($file['tmp_name']);

        if ($mimeFromContent !== $mime) {
            throw new PuzzleImageProcessorException('File content does not match declared type. Possible malicious file.');
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new PuzzleImageProcessorException('File is not a valid image.');
        }

        $size = (int) $file['size'];

        foreach ($allowedFormats as $format) {
            $mimes = array_map('trim', explode(',', $format['mime_types']));
            $extensions = array_map('trim', explode(',', $format['extensions']));

            if ($format['active'] && in_array($mime, $mimes, true)) {
                if ($format['max_size'] > 0 && $size > ((int) $format['max_size'] * 1024 * 1024)) {
                    throw new PuzzleImageProcessorException('Il file supera la dimensione massima consentita.');
                }

                $extension = Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, $extensions, true)) {
                    throw new PuzzleImageProcessorException('Estensione non supportata.');
                }

                $this->scanForViruses($file['tmp_name']);

                return true;
            }
        }

        throw new PuzzleImageProcessorException('Formato non supportato.');
    }

    /**
     * Copia il file nella cartella finale.
     */
    public function moveToCustomizationDirectory($tmpPath, $destination)
    {
        $dir = dirname($destination);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (!@rename($tmpPath, $destination)) {
            throw new PuzzleImageProcessorException('Impossibile salvare il file.');
        }
    }

    /**
     * Scan file for malicious content.
     *
     * @throws PuzzleImageProcessorException
     */
    protected function scanForViruses($filePath)
    {
        if (function_exists('socket_create')) {
            $socket = @socket_create(AF_UNIX, SOCK_STREAM, 0);
            if ($socket && @socket_connect($socket, '/var/run/clamav/clamd.sock')) {
                socket_write($socket, 'SCAN ' . $filePath . "\n");
                $response = socket_read($socket, 1024);
                socket_close($socket);

                if ($response !== false && strpos($response, 'FOUND') !== false) {
                    throw new PuzzleImageProcessorException('Virus detected in uploaded file.');
                }
            }
        }

        if (function_exists('exec')) {
            $output = [];
            $return = 0;
            @exec('clamscan --no-summary ' . escapeshellarg($filePath), $output, $return);

            if ($return === 1) {
                throw new PuzzleImageProcessorException('Virus detected in uploaded file.');
            }
        }

        $content = file_get_contents($filePath, false, null, 0, 1024);
        $maliciousPatterns = [
            '/<\?php.*eval.*\?>/i',
            '/base64_decode.*eval/i',
            '/system\s*\(/i',
            '/exec\s*\(/i',
            '/passthru\s*\(/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new PuzzleImageProcessorException('Malicious code detected in file.');
            }
        }
    }

    /**
     * Convert image to target format.
     */
    public function convertImage($sourcePath, $targetPath, $targetFormat = 'jpeg')
    {
        if (!extension_loaded('imagick') && !extension_loaded('gd')) {
            throw new PuzzleImageProcessorException('No image processing library available (ImageMagick or GD required).');
        }

        if (extension_loaded('imagick')) {
            return $this->convertWithImageMagick($sourcePath, $targetPath, $targetFormat);
        }

        return $this->convertWithGD($sourcePath, $targetPath, $targetFormat);
    }

    protected function convertWithImageMagick($sourcePath, $targetPath, $targetFormat)
    {
        try {
            $image = new Imagick($sourcePath);
            $image->setImageFormat($targetFormat);

            if ($targetFormat === 'jpeg' || $targetFormat === 'jpg') {
                $image->setImageCompression(Imagick::COMPRESSION_JPEG);
                $image->setImageCompressionQuality(95);
            } elseif ($targetFormat === 'png') {
                $image->setImageCompression(Imagick::COMPRESSION_ZIP);
                $image->setImageCompressionQuality(9);
            } elseif ($targetFormat === 'webp') {
                $image->setImageCompressionQuality(90);
            }

            $image->writeImage($targetPath);
            $image->clear();
            $image->destroy();

            return true;
        } catch (Exception $e) {
            throw new PuzzleImageProcessorException('Image conversion failed: ' . $e->getMessage());
        }
    }

    protected function convertWithGD($sourcePath, $targetPath, $targetFormat)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new PuzzleImageProcessorException('Cannot detect image format.');
        }

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $source = imagecreatefromwebp($sourcePath);
                    break;
                }
            default:
                throw new PuzzleImageProcessorException('Unsupported image format for GD conversion.');
        }

        if (!$source) {
            throw new PuzzleImageProcessorException('Failed to load source image.');
        }

        $success = false;
        switch ($targetFormat) {
            case 'jpeg':
            case 'jpg':
                $success = imagejpeg($source, $targetPath, 95);
                break;
            case 'png':
                $success = imagepng($source, $targetPath, 9);
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    $success = imagewebp($source, $targetPath, 90);
                }
                break;
        }

        imagedestroy($source);

        if (!$success) {
            throw new PuzzleImageProcessorException('Failed to save converted image.');
        }

        return true;
    }

    /**
     * Automatically convert image if needed.
     */
    public function autoConvert($sourcePath, $formatConfig)
    {
        $sourceExt = Tools::strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

        $conversionMap = [
            'heic' => 'jpeg',
            'heif' => 'jpeg',
            'bmp' => 'png',
            'tif' => 'jpeg',
            'tiff' => 'jpeg',
        ];

        if (!isset($conversionMap[$sourceExt])) {
            return $sourcePath;
        }

        $targetFormat = $conversionMap[$sourceExt];
        $targetPath = preg_replace('/\.[^.]+$/', '.' . $targetFormat, $sourcePath);

        $this->convertImage($sourcePath, $targetPath, $targetFormat);
        @unlink($sourcePath);

        return $targetPath;
    }

    /**
     * Validate image DPI.
     */
    public function validateDPI($imagePath, $minimumDPI = 300)
    {
        $dpi = $this->getImageDPI($imagePath);

        $result = [
            'valid' => $dpi >= $minimumDPI,
            'actual_dpi' => $dpi,
            'message' => '',
        ];

        if ($dpi < $minimumDPI) {
            if ($dpi < 150) {
                $result['message'] = sprintf(
                    'Very low DPI (%d). Print quality will be very poor. Minimum recommended: %d DPI',
                    $dpi,
                    $minimumDPI
                );
            } elseif ($dpi < 200) {
                $result['message'] = sprintf(
                    'Low DPI (%d). Print quality may be compromised. Recommended: %d DPI',
                    $dpi,
                    $minimumDPI
                );
            } else {
                $result['message'] = sprintf(
                    'DPI (%d) is below recommended %d DPI. Quality may be affected.',
                    $dpi,
                    $minimumDPI
                );
            }
        } else {
            $result['message'] = sprintf('Good DPI (%d). Suitable for print.', $dpi);
        }

        return $result;
    }

    protected function getImageDPI($imagePath)
    {
        if (extension_loaded('imagick')) {
            try {
                $image = new Imagick($imagePath);
                $resolution = $image->getImageResolution();
                $image->clear();
                $image->destroy();

                return (int) (($resolution['x'] + $resolution['y']) / 2);
            } catch (Exception $e) {
                // Continue to EXIF fallback
            }
        }

        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($imagePath);
            if ($exif && isset($exif['XResolution'])) {
                if (is_string($exif['XResolution']) && strpos($exif['XResolution'], '/') !== false) {
                    list($num, $denom) = explode('/', $exif['XResolution']);
                    if ((int) $denom !== 0) {
                        return (int) ($num / $denom);
                    }
                }

                return (int) $exif['XResolution'];
            }
        }

        return 72;
    }

    public function optimizeDPI($imagePath, $targetDPI = 300)
    {
        if (!extension_loaded('imagick')) {
            throw new PuzzleImageProcessorException('ImageMagick required for DPI optimization.');
        }

        try {
            $image = new Imagick($imagePath);
            $image->setImageResolution($targetDPI, $targetDPI);
            $image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);

            $currentResolution = $image->getImageResolution();
            $currentDPI = ($currentResolution['x'] + $currentResolution['y']) / 2;

            if ($currentDPI < $targetDPI) {
                $scaleFactor = $targetDPI / max($currentDPI, 1);
                $newWidth = (int) ($image->getImageWidth() * $scaleFactor);
                $newHeight = (int) ($image->getImageHeight() * $scaleFactor);

                $image->resampleImage($newWidth, $newHeight, $targetDPI, $targetDPI);
            }

            $image->writeImage($imagePath);
            $image->clear();
            $image->destroy();

            return true;
        } catch (Exception $e) {
            throw new PuzzleImageProcessorException('DPI optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate resolution against puzzle dimensions.
     */
    public function validateResolution($imagePath, $puzzleDimensions, $targetDPI = 300)
    {
        $imageSize = getimagesize($imagePath);
        if (!$imageSize) {
            throw new PuzzleImageProcessorException('Cannot read image dimensions.');
        }

        $actualWidth = (int) $imageSize[0];
        $actualHeight = (int) $imageSize[1];

        $requiredWidth = (int) (($puzzleDimensions['width_mm'] / 25.4) * $targetDPI);
        $requiredHeight = (int) (($puzzleDimensions['height_mm'] / 25.4) * $targetDPI);

        $result = [
            'valid' => ($actualWidth >= $requiredWidth && $actualHeight >= $requiredHeight),
            'message' => '',
            'required' => [
                'width' => $requiredWidth,
                'height' => $requiredHeight,
            ],
            'actual' => [
                'width' => $actualWidth,
                'height' => $actualHeight,
            ],
        ];

        if (!$result['valid']) {
            $widthPercent = (int) (($actualWidth / max($requiredWidth, 1)) * 100);
            $heightPercent = (int) (($actualHeight / max($requiredHeight, 1)) * 100);

            $result['message'] = sprintf(
                'Image resolution too low for print quality. Required: %dx%d pixels. Actual: %dx%d pixels (%d%% width, %d%% height). Print quality will be significantly degraded.',
                $requiredWidth,
                $requiredHeight,
                $actualWidth,
                $actualHeight,
                $widthPercent,
                $heightPercent
            );
        } else {
            $result['message'] = sprintf(
                'Image resolution is sufficient (%dx%d pixels for %dx%d mm at %d DPI)',
                $actualWidth,
                $actualHeight,
                $puzzleDimensions['width_mm'],
                $puzzleDimensions['height_mm'],
                $targetDPI
            );
        }

        return $result;
    }

    /**
     * Create optimized thumbnail.
     */
    public function createThumbnail($sourcePath, $targetPath, $maxWidth = 800, $maxHeight = 600)
    {
        if (extension_loaded('imagick')) {
            return $this->createThumbnailImageMagick($sourcePath, $targetPath, $maxWidth, $maxHeight);
        }

        return $this->createThumbnailGD($sourcePath, $targetPath, $maxWidth, $maxHeight);
    }

    protected function createThumbnailImageMagick($sourcePath, $targetPath, $maxWidth, $maxHeight)
    {
        try {
            $image = new Imagick($sourcePath);
            $image->thumbnailImage($maxWidth, $maxHeight, true);
            $image->setImageCompression(Imagick::COMPRESSION_JPEG);
            $image->setImageCompressionQuality(85);
            $image->stripImage();
            $image->writeImage($targetPath);
            $image->clear();
            $image->destroy();

            return true;
        } catch (Exception $e) {
            throw new PuzzleImageProcessorException('Thumbnail creation failed: ' . $e->getMessage());
        }
    }

    protected function createThumbnailGD($sourcePath, $targetPath, $maxWidth, $maxHeight)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new PuzzleImageProcessorException('Cannot read image for thumbnail.');
        }

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new PuzzleImageProcessorException('Unsupported format for thumbnail.');
        }

        if (!$source) {
            throw new PuzzleImageProcessorException('Failed to load image for thumbnail.');
        }

        $srcWidth = imagesx($source);
        $srcHeight = imagesy($source);

        $ratio = min($maxWidth / max($srcWidth, 1), $maxHeight / max($srcHeight, 1));
        $newWidth = (int) ($srcWidth * $ratio);
        $newHeight = (int) ($srcHeight * $ratio);

        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

        if ($imageInfo[2] === IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled(
            $thumbnail,
            $source,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $srcWidth,
            $srcHeight
        );

        imagejpeg($thumbnail, $targetPath, 85);

        imagedestroy($source);
        imagedestroy($thumbnail);

        return true;
    }

    /**
     * Add watermark to image.
     */
    public function addWatermark($imagePath, $watermarkPath, $position = 'center', $opacity = 50)
    {
        if (!file_exists($watermarkPath)) {
            throw new PuzzleImageProcessorException('Watermark file not found.');
        }

        if (extension_loaded('imagick')) {
            return $this->addWatermarkImageMagick($imagePath, $watermarkPath, $position, $opacity);
        }

        return $this->addWatermarkGD($imagePath, $watermarkPath, $position, $opacity);
    }

    protected function addWatermarkImageMagick($imagePath, $watermarkPath, $position, $opacity)
    {
        try {
            $image = new Imagick($imagePath);
            $watermark = new Imagick($watermarkPath);

            $watermark->evaluateImage(Imagick::EVALUATE_MULTIPLY, $opacity / 100, Imagick::CHANNEL_ALPHA);

            list($x, $y) = $this->calculateWatermarkPosition(
                $image->getImageWidth(),
                $image->getImageHeight(),
                $watermark->getImageWidth(),
                $watermark->getImageHeight(),
                $position
            );

            $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);
            $image->writeImage($imagePath);

            $image->clear();
            $image->destroy();
            $watermark->clear();
            $watermark->destroy();

            return true;
        } catch (Exception $e) {
            throw new PuzzleImageProcessorException('Watermark addition failed: ' . $e->getMessage());
        }
    }

    protected function addWatermarkGD($imagePath, $watermarkPath, $position, $opacity)
    {
        $imageInfo = getimagesize($imagePath);
        $watermarkInfo = getimagesize($watermarkPath);

        if (!$imageInfo || !$watermarkInfo) {
            throw new PuzzleImageProcessorException('Cannot load images for watermarking.');
        }

        $image = imagecreatefromstring(file_get_contents($imagePath));
        $watermark = imagecreatefrompng($watermarkPath);

        if (!$image || !$watermark) {
            throw new PuzzleImageProcessorException('Failed to create images for watermark.');
        }

        list($x, $y) = $this->calculateWatermarkPosition(
            imagesx($image),
            imagesy($image),
            imagesx($watermark),
            imagesy($watermark),
            $position
        );

        imagecopymerge(
            $image,
            $watermark,
            (int) $x,
            (int) $y,
            0,
            0,
            imagesx($watermark),
            imagesy($watermark),
            $opacity
        );

        imagepng($image, $imagePath);

        imagedestroy($image);
        imagedestroy($watermark);

        return true;
    }

    protected function calculateWatermarkPosition($imgW, $imgH, $wmW, $wmH, $position)
    {
        $margin = 20;

        switch ($position) {
            case 'top-left':
                return [$margin, $margin];
            case 'top-right':
                return [$imgW - $wmW - $margin, $margin];
            case 'bottom-left':
                return [$margin, $imgH - $wmH - $margin];
            case 'bottom-right':
                return [$imgW - $wmW - $margin, $imgH - $wmH - $margin];
            case 'center':
            default:
                return [($imgW - $wmW) / 2, ($imgH - $wmH) / 2];
        }
    }

    /**
     * Generate production-ready file.
     */
    public function generateProductionFile($sourcePath, $config)
    {
        if (!extension_loaded('imagick')) {
            throw new PuzzleImageProcessorException('ImageMagick required for production file generation.');
        }

        try {
            $image = new Imagick($sourcePath);
            $image->setImageColorspace(Imagick::COLORSPACE_CMYK);
            $image->setImageResolution(300, 300);
            $image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
            $image->borderImage('white', 35, 35);
            $image->setImageFormat('tiff');
            $image->setImageCompression(Imagick::COMPRESSION_LZW);

            $productionPath = preg_replace('/\.[^.]+$/', '_production.tiff', $sourcePath);
            $image->writeImage($productionPath);

            $image->clear();
            $image->destroy();

            return $productionPath;
        } catch (Exception $e) {
            throw new PuzzleImageProcessorException('Production file generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate a full production package and return ZIP path.
     */
    public function generateProductionPackage($customizationId, $config)
    {
        $customization = new PuzzleCustomization($customizationId);
        if (!Validate::isLoadedObject($customization)) {
            throw new PuzzleImageProcessorException('Customization not found.');
        }

        $imagePath = _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/customizations/' . $customization->image_path;
        if (!file_exists($imagePath)) {
            throw new PuzzleImageProcessorException('Original image not found.');
        }

        $tempDir = _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/temp/production_' . $customizationId . '/';
        @mkdir($tempDir, 0755, true);

        copy($imagePath, $tempDir . 'original.' . pathinfo($imagePath, PATHINFO_EXTENSION));

        $productionFile = $this->generateProductionFile($imagePath, $config);
        @rename($productionFile, $tempDir . 'puzzle-print-300dpi.tiff');

        $boxFront = $this->generateBoxFront($imagePath, $config);
        @rename($boxFront, $tempDir . 'box-front-300dpi.tiff');

        $boxBack = $this->generateBoxBack($config);
        @rename($boxBack, $tempDir . 'box-back-300dpi.tiff');

        file_put_contents(
            $tempDir . 'config.json',
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $this->generateProductionSpecsPDF($tempDir . 'production-specs.pdf', $config);

        $zipPath = _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/production/order_' . $customizationId . '.zip';
        @mkdir(dirname($zipPath), 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new PuzzleImageProcessorException('Cannot create ZIP file.');
        }

        $files = scandir($tempDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $zip->addFile($tempDir . $file, $file);
        }

        $zip->close();

        foreach (glob($tempDir . '*') as $file) {
            @unlink($file);
        }
        @rmdir($tempDir);

        return $zipPath;
    }

    protected function generateBoxFront($imagePath, $config)
    {
        if (!extension_loaded('imagick')) {
            throw new PuzzleImageProcessorException('ImageMagick required for box artwork generation.');
        }

        $image = new Imagick($imagePath);
        $image->setImageResolution(300, 300);
        $image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
        $image->setImageFormat('tiff');

        if (isset($config['text_content']) && $config['text_content']) {
            $draw = new ImagickDraw();
            $draw->setFillColor(new ImagickPixel('#000000'));
            $draw->setFontSize(48);
            $draw->setTextAlignment(Imagick::ALIGN_CENTER);
            $image->annotateImage($draw, $image->getImageWidth() / 2, $image->getImageHeight() - 100, 0, $config['text_content']);
        }

        $outputPath = preg_replace('/\.[^.]+$/', '_box_front.tiff', $imagePath);
        $image->writeImage($outputPath);
        $image->clear();
        $image->destroy();

        return $outputPath;
    }

    protected function generateBoxBack($config)
    {
        if (!extension_loaded('imagick')) {
            throw new PuzzleImageProcessorException('ImageMagick required for box artwork generation.');
        }

        $width = 2480;
        $height = 3508;
        $image = new Imagick();
        $image->newImage($width, $height, new ImagickPixel('#ffffff'));
        $image->setImageFormat('tiff');

        $draw = new ImagickDraw();
        $draw->setFillColor(new ImagickPixel('#000000'));
        $draw->setFontSize(36);

        $text = 'Puzzle Specifications:' . "\n";
        if (isset($config['pieces'])) {
            $text .= 'Pieces: ' . (int) $config['pieces'] . "\n";
        }
        if (isset($config['dimension'])) {
            $text .= 'Dimension: ' . (int) $config['dimension'] . ' mm' . "\n";
        }

        $image->annotateImage($draw, 200, 200, 0, $text);

        $outputPath = _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/temp/box_back_' . uniqid() . '.tiff';
        $image->writeImage($outputPath);
        $image->clear();
        $image->destroy();

        return $outputPath;
    }

    protected function generateProductionSpecsPDF($destination, $config)
    {
        if (class_exists('TCPDF')) {
            $pdf = new TCPDF();
            $pdf->AddPage();
            $html = '<h1>Puzzle Production Specs</h1>';
            foreach ($config as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $html .= '<p><strong>' . Tools::safeOutput($key) . ':</strong> ' . Tools::safeOutput((string) $value) . '</p>';
            }
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output($destination, 'F');
        } else {
            $content = "Puzzle Production Specs\n";
            foreach ($config as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $content .= strtoupper($key) . ': ' . $value . "\n";
            }
            file_put_contents($destination, $content);
        }
    }
}
