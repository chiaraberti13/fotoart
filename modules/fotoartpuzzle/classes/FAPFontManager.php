<?php

class FAPFontManager
{
    /**
     * Return available fonts from filesystem.
     *
     * @return array
     */
    public function getAvailableFonts()
    {
        $fonts = [];
        foreach ($this->getFontFiles() as $file) {
            $fonts[] = [
                'name' => pathinfo($file, PATHINFO_FILENAME),
                'filename' => basename($file),
                'path' => $file,
            ];
        }

        return $fonts;
    }

    /**
     * Resolve font path from configuration name.
     *
     * @param string $font
     *
     * @return string|null
     */
    public function resolveFontPath($font)
    {
        $font = trim((string) $font);
        if (!$font) {
            return null;
        }

        foreach ($this->getFontFiles() as $file) {
            if (strcasecmp(pathinfo($file, PATHINFO_FILENAME), $font) === 0) {
                return $file;
            }
        }

        $candidate = _PS_MODULE_DIR_ . FotoArtPuzzle::MODULE_NAME . '/fonts/' . basename($font);
        if (file_exists($candidate)) {
            return $candidate;
        }

        return null;
    }

    /**
     * Persist uploaded font file in module directory.
     *
     * @param array $file
     *
     * @return array
     */
    public function handleUpload(array $file)
    {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Invalid font upload');
        }

        $extension = Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['ttf', 'otf'])) {
            throw new Exception('Unsupported font type');
        }

        $fontDir = _PS_MODULE_DIR_ . FotoArtPuzzle::MODULE_NAME . '/fonts';
        if (!is_dir($fontDir)) {
            @mkdir($fontDir, 0750, true);
        }

        $safeName = Tools::link_rewrite(pathinfo($file['name'], PATHINFO_FILENAME));
        $destination = $fontDir . '/' . $safeName . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Unable to move uploaded font');
        }

        return [
            'name' => $safeName,
            'filename' => basename($destination),
            'path' => $destination,
        ];
    }

    /**
     * Retrieve list of font files.
     *
     * @return array
     */
    private function getFontFiles()
    {
        $fontDir = _PS_MODULE_DIR_ . FotoArtPuzzle::MODULE_NAME . '/fonts';
        if (!is_dir($fontDir)) {
            return [];
        }

        $files = glob($fontDir . '/*.{ttf,otf}', GLOB_BRACE);
        return $files ?: [];
    }
}

