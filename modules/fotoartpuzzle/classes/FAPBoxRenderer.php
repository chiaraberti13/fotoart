<?php

class FAPBoxRenderer
{
    /**
     * Render box preview overlay for backward compatibility.
     *
     * @param string $imagePath
     * @param string $text
     * @param string $color
     * @param string $font
     * @param string $destination
     *
     * @return string
     */
    public function render($imagePath, $text, $color, $font, $destination)
    {
        return $this->renderFromImage($imagePath, $destination, [
            'text' => $text,
            'color' => $color,
            'font' => $font,
        ]);
    }

    /**
     * Render a realistic box layout starting from the uploaded asset.
     *
     * @param string $imagePath
     * @param string $destination
     * @param array $options
     *
     * @return string
     */
    public function renderFromImage($imagePath, $destination, array $options = [])
    {
        $template = $this->loadTemplate(isset($options['template']) ? $options['template'] : null);
        $fontPath = $this->resolveFontPath(isset($options['font']) ? $options['font'] : null);
        $textColor = $this->resolveColor(isset($options['color']) ? $options['color'] : '#ffffff');
        $text = Tools::substr(isset($options['text']) ? (string) $options['text'] : '', 0, 120);

        $photo = $this->createImageFromPath($imagePath);
        if (!$photo) {
            throw new Exception('Unable to load source image for box rendering');
        }

        $targetWidth = $template ? imagesx($template) : 1024;
        $targetHeight = $template ? imagesy($template) : 768;
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        $background = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $background);

        if ($template) {
            imagecopy($canvas, $template, 0, 0, 0, 0, $targetWidth, $targetHeight);
        }

        $photoArea = $this->getPhotoArea($targetWidth, $targetHeight);
        $resizedPhoto = $this->resizeImage($photo, $photoArea['width'], $photoArea['height']);
        imagecopy(
            $canvas,
            $resizedPhoto,
            $photoArea['x'],
            $photoArea['y'],
            0,
            0,
            imagesx($resizedPhoto),
            imagesy($resizedPhoto)
        );

        $this->drawText($canvas, $text, $fontPath, $textColor, $photoArea);

        imagepng($canvas, $destination, 6);

        imagedestroy($canvas);
        imagedestroy($photo);
        imagedestroy($resizedPhoto);
        if ($template) {
            imagedestroy($template);
        }

        return $destination;
    }

    /**
     * Load template file if available.
     *
     * @param string|null $templateName
     *
     * @return resource|false
     */
    private function loadTemplate($templateName)
    {
        $paths = [];
        if ($templateName) {
            $paths[] = rtrim(FAPPathBuilder::getBoxesPath(), '/\\') . '/' . basename($templateName);
            $paths[] = _PS_MODULE_DIR_ . FotoArtPuzzle::MODULE_NAME . '/views/img/boxes/' . basename($templateName);
        }
        $paths[] = _PS_MODULE_DIR_ . FotoArtPuzzle::MODULE_NAME . '/views/img/boxes/default.png';

        foreach ($paths as $path) {
            if ($path && file_exists($path)) {
                return imagecreatefrompng($path);
            }
        }

        return false;
    }

    /**
     * Resolve color definition as RGB array.
     *
     * @param string $color
     *
     * @return array
     */
    private function resolveColor($color)
    {
        $hex = ltrim($color ?: '#ffffff', '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Load GD resource from path.
     *
     * @param string $path
     *
     * @return resource|false
     */
    private function createImageFromPath($path)
    {
        $info = @getimagesize($path);
        if (!$info) {
            return false;
        }

        switch ($info['mime']) {
            case 'image/png':
                $image = imagecreatefrompng($path);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                return $image;
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/jpeg':
            case 'image/jpg':
            default:
                return imagecreatefromjpeg($path);
        }
    }

    /**
     * Compute area where photo should be placed.
     *
     * @param int $width
     * @param int $height
     *
     * @return array
     */
    private function getPhotoArea($width, $height)
    {
        $margin = (int) ($width * 0.08);
        $availableHeight = (int) ($height * 0.65);

        return [
            'x' => $margin,
            'y' => $margin,
            'width' => $width - (2 * $margin),
            'height' => $availableHeight,
        ];
    }

    /**
     * Resize image to fit target size preserving ratio.
     *
     * @param resource $image
     * @param int $targetWidth
     * @param int $targetHeight
     *
     * @return resource
     */
    private function resizeImage($image, $targetWidth, $targetHeight)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min($targetWidth / $width, $targetHeight / $height, 1);
        $newWidth = (int) round($width * $scale);
        $newHeight = (int) round($height * $scale);

        $resampled = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resampled, false);
        imagesavealpha($resampled, true);
        imagecopyresampled($resampled, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $resampled;
    }

    /**
     * Draw text on the canvas.
     *
     * @param resource $canvas
     * @param string $text
     * @param string|null $fontPath
     * @param array $color
     * @param array $photoArea
     */
    private function drawText($canvas, $text, $fontPath, array $color, array $photoArea)
    {
        if (!$text) {
            return;
        }

        $textColor = imagecolorallocate($canvas, $color[0], $color[1], $color[2]);
        $baseline = $photoArea['y'] + $photoArea['height'] + (int) ($photoArea['height'] * 0.15);

        if ($fontPath && file_exists($fontPath) && function_exists('imagettfbbox')) {
            $fontSize = 28;
            $angle = 0;
            $box = imagettfbbox($fontSize, $angle, $fontPath, $text);
            $textWidth = abs($box[2] - $box[0]);
            $x = (imagesx($canvas) - $textWidth) / 2;
            $y = $baseline + $fontSize;
            imagettftext($canvas, $fontSize, $angle, (int) $x, (int) $y, $textColor, $fontPath, $text);
            return;
        }

        $font = 5;
        $textWidth = imagefontwidth($font) * Tools::strlen($text);
        $x = (imagesx($canvas) - $textWidth) / 2;
        $y = $baseline;
        imagestring($canvas, $font, (int) $x, (int) $y, $text, $textColor);
    }

    /**
     * Resolve font path from configuration.
     *
     * @param string|null $fontName
     *
     * @return string|null
     */
    private function resolveFontPath($fontName)
    {
        if (!$fontName) {
            return null;
        }

        $fontManager = new FAPFontManager();
        return $fontManager->resolveFontPath($fontName);
    }
}
