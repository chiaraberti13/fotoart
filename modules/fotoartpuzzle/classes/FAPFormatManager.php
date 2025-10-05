<?php

class FAPFormatManager
{
    /**
     * Validate that uploaded image fits selected format
     *
     * @param array $format
     * @param int $width
     * @param int $height
     *
     * @return bool
     */
    public function isValidForFormat(array $format, $width, $height)
    {
        if (empty($format['width']) || empty($format['height'])) {
            return false;
        }

        $ratioFormat = $format['width'] / $format['height'];
        $ratioImage = $width / $height;

        return abs($ratioFormat - $ratioImage) < 0.1;
    }

    /**
     * Calculate DPI for format selection
     *
     * @param array $format
     * @param int $width
     * @param int $height
     *
     * @return float|null
     */
    public function calculateDpi(array $format, $width, $height)
    {
        if (empty($format['width']) || empty($format['height'])) {
            return null;
        }

        $dpiX = $width / ($format['width'] / 300);
        $dpiY = $height / ($format['height'] / 300);

        return min($dpiX, $dpiY);
    }

    /**
     * Retrieve formats as array
     *
     * @return array
     */
    public function getFormats()
    {
        $formats = json_decode((string) Configuration::get(FAPConfiguration::FORMATS), true);
        if (!is_array($formats)) {
            return [];
        }

        return $formats;
    }
}
