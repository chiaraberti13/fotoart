<?php

class FAPBoxRenderer
{
    /**
     * Render box preview overlay
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
        $content = file_get_contents($imagePath);
        if ($content === false) {
            throw new Exception('Unable to read image.');
        }
        $base = imagecreatefromstring($content);
        $width = imagesx($base);
        $height = imagesy($base);

        $overlayHeight = (int) ($height * 0.2);
        $overlay = imagecreatetruecolor($width, $overlayHeight);

        list($r, $g, $b) = $this->hexToRgb($color);
        $backgroundColor = imagecolorallocatealpha($overlay, 0, 0, 0, 80);
        imagefilledrectangle($overlay, 0, 0, $width, $overlayHeight, $backgroundColor);

        $textColor = imagecolorallocate($overlay, $r, $g, $b);
        $fontSize = 18;
        $fontFile = _PS_MODULE_DIR_ . FotoArtPuzzle::MODULE_NAME . '/fonts/' . basename($font) . '.ttf';
        $textSnippet = Tools::substr($text, 0, 60);

        if ($fontFile && file_exists($fontFile) && function_exists('imagettftext')) {
            imagettftext($overlay, $fontSize, 0, 20, (int) ($overlayHeight / 1.5), $textColor, $fontFile, $textSnippet);
        } else {
            imagestring($overlay, 5, 20, (int) ($overlayHeight / 2) - 6, $textSnippet, $textColor);
        }

        imagecopy($base, $overlay, 0, $height - $overlayHeight, 0, 0, $width, $overlayHeight);

        imagejpeg($base, $destination, 90);

        imagedestroy($overlay);
        imagedestroy($base);

        return $destination;
    }

    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
