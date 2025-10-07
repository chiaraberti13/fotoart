<?php

class FAPQualityService
{
    private const QUALITY_EXCELLENT = 4;
    private const QUALITY_GREAT = 3;
    private const QUALITY_GOOD = 2;
    private const QUALITY_POOR = 1;
    private const QUALITY_INSUFFICIENT = 0;

    private const DPI_EXCELLENT = 96;
    private const DPI_GREAT = 72;
    private const DPI_GOOD = 48;
    private const DPI_POOR = 36;

    /**
     * Evaluate a list of formats for a given image size.
     *
     * @param int $imageWidth
     * @param int $imageHeight
     * @param array $formats
     *
     * @return array
     */
    public function evaluateFormats($imageWidth, $imageHeight, array $formats)
    {
        $evaluations = [];

        foreach ($formats as $format) {
            $evaluation = $this->evaluateFormat($imageWidth, $imageHeight, $format);
            if ($evaluation) {
                $evaluations[] = $evaluation;
            }
        }

        return $evaluations;
    }

    /**
     * Determine if at least one format is printable.
     *
     * @param array $evaluations
     *
     * @return bool
     */
    public function hasPrintableFormat(array $evaluations)
    {
        foreach ($evaluations as $evaluation) {
            if ($this->isPrintableQuality(isset($evaluation['quality']) ? (int) $evaluation['quality'] : null)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluate a single format.
     *
     * @param int $imageWidth
     * @param int $imageHeight
     * @param array $format
     *
     * @return array|null
     */
    public function evaluateFormat($imageWidth, $imageHeight, array $format)
    {
        $dimensions = $this->extractDimensions($format);
        $printWidth = $dimensions['width'];
        $printHeight = $dimensions['height'];

        if ($printWidth <= 0 || $printHeight <= 0) {
            return null;
        }

        $quality = $this->calculateQuality($printWidth, $printHeight, $imageWidth, $imageHeight);
        $coordinates = $this->buildCoordinates($imageWidth, $imageHeight, $printWidth, $printHeight, isset($format['shape']) ? $format['shape'] : null);

        return array_merge($format, [
            'quality' => $quality,
            'coordinates' => $coordinates,
        ]);
    }

    /**
     * Calculate print quality score using DPI averages.
     *
     * @param float $printWidthCm
     * @param float $printHeightCm
     * @param int $pixelWidth
     * @param int $pixelHeight
     *
     * @return int
     */
    public function calculateQuality($printWidthCm, $printHeightCm, $pixelWidth, $pixelHeight)
    {
        if ($printWidthCm <= 0 || $printHeightCm <= 0 || $pixelWidth <= 0 || $pixelHeight <= 0) {
            return self::QUALITY_INSUFFICIENT;
        }

        if ($printWidthCm >= $printHeightCm) {
            $estimatedPixelHeight = ($pixelWidth * $printHeightCm) / ($printWidthCm ?: 1);
            $dpiWidth = $pixelWidth / ($printWidthCm / 2.54);
            $dpiHeight = $estimatedPixelHeight / ($printHeightCm / 2.54);
        } else {
            $estimatedPixelWidth = ($printWidthCm * $pixelHeight) / ($printHeightCm ?: 1);
            $dpiWidth = $pixelHeight / ($printWidthCm / 2.54);
            $dpiHeight = $estimatedPixelWidth / ($printHeightCm / 2.54);
        }

        $avgDpi = ($dpiWidth + $dpiHeight) / 2;

        if ($avgDpi >= self::DPI_EXCELLENT) {
            return self::QUALITY_EXCELLENT;
        }

        if ($avgDpi >= self::DPI_GREAT) {
            return self::QUALITY_GREAT;
        }

        if ($avgDpi >= self::DPI_GOOD) {
            return self::QUALITY_GOOD;
        }

        if ($avgDpi >= self::DPI_POOR) {
            return self::QUALITY_POOR;
        }

        return self::QUALITY_INSUFFICIENT;
    }

    /**
     * Check if the quality score is printable.
     *
     * @param int|null $quality
     *
     * @return bool
     */
    public function isPrintableQuality($quality)
    {
        return $quality !== null && $quality >= self::QUALITY_POOR;
    }

    /**
     * Build landscape and portrait coordinates for default crop.
     *
     * @param int $imageWidth
     * @param int $imageHeight
     * @param float $printWidthCm
     * @param float $printHeightCm
     * @param string|null $shape
     *
     * @return array
     */
    public function buildCoordinates($imageWidth, $imageHeight, $printWidthCm, $printHeightCm, $shape = null)
    {
        return [
            'landscape' => $this->buildLandscapeSelection($imageWidth, $imageHeight, $printWidthCm, $printHeightCm, 'landscape', $shape),
            'portrait' => $this->buildPortraitSelection($imageWidth, $imageHeight, $printWidthCm, $printHeightCm, 'portrait', $shape),
        ];
    }

    /**
     * Extract printable dimensions from the format payload.
     *
     * @param array $format
     *
     * @return array
     */
    private function extractDimensions(array $format)
    {
        $dpi = isset($format['dpi']) ? (float) $format['dpi'] : null;
        $candidates = [
            ['width', 'height'],
            ['print_width', 'print_height'],
            ['printWidth', 'printHeight'],
            ['width_cm', 'height_cm'],
            ['widthCm', 'heightCm'],
            ['larg', 'alt'],
        ];

        foreach ($candidates as $pair) {
            list($widthKey, $heightKey) = $pair;
            if (!empty($format[$widthKey]) && !empty($format[$heightKey])) {
                $width = $this->normaliseMeasurement($format[$widthKey], $dpi);
                $height = $this->normaliseMeasurement($format[$heightKey], $dpi);
                if ($width > 0 && $height > 0) {
                    return [
                        'width' => $width,
                        'height' => $height,
                    ];
                }
            }
        }

        $dimensionKeys = ['dimensioni', 'dimensions'];
        foreach ($dimensionKeys as $dimensionKey) {
            if (!empty($format[$dimensionKey]) && is_string($format[$dimensionKey])) {
                $parts = preg_split('/x|×/i', $format[$dimensionKey]);
                if (count($parts) >= 2) {
                    $width = $this->normaliseMeasurement($parts[0], $dpi);
                    $height = $this->normaliseMeasurement($parts[1], $dpi);
                    if ($width > 0 && $height > 0) {
                        return [
                            'width' => $width,
                            'height' => $height,
                        ];
                    }
                }
            }
        }

        return [
            'width' => 0,
            'height' => 0,
        ];
    }

    /**
     * Build default selection for landscape orientation.
     *
     * @param int $imageWidth
     * @param int $imageHeight
     * @param float $printWidth
     * @param float $printHeight
     * @param string $orientation
     * @param string|null $shape
     *
     * @return array
     */
    private function buildLandscapeSelection($imageWidth, $imageHeight, $printWidth, $printHeight, $orientation, $shape)
    {
        $selectionWidth = ($imageHeight * $printWidth) / ($printHeight ?: 1);
        if ($selectionWidth > $imageWidth) {
            $selectionWidth = $imageWidth;
            $x = 0;
        } else {
            $x = ($imageWidth - $selectionWidth) / 2;
        }

        $selectionHeight = ($imageWidth * $printHeight) / ($printWidth ?: 1);
        if ($selectionHeight > $imageHeight) {
            $selectionHeight = $imageHeight;
            $y = 0;
        } else {
            $y = ($imageHeight - $selectionHeight) / 2;
        }

        $x2 = $selectionWidth + $x;
        $y2 = $selectionHeight + $y;

        $ratio = $printHeight > 0 ? $printWidth / $printHeight : 1;

        return [
            'w' => (int) round($imageWidth),
            'h' => (int) round($imageHeight),
            'x' => (int) round($x),
            'y' => (int) round($y),
            'x2' => (int) round($x2),
            'y2' => (int) round($y2),
            'ratio' => $ratio,
            'printW' => $printWidth,
            'printH' => $printHeight,
            'orientation' => $orientation,
            'shape' => $shape,
        ];
    }

    /**
     * Build default selection for portrait orientation.
     *
     * @param int $imageWidth
     * @param int $imageHeight
     * @param float $printWidth
     * @param float $printHeight
     * @param string $orientation
     * @param string|null $shape
     *
     * @return array
     */
    private function buildPortraitSelection($imageWidth, $imageHeight, $printWidth, $printHeight, $orientation, $shape)
    {
        $swappedWidth = $printHeight;
        $swappedHeight = $printWidth;

        $selectionWidth = ($imageHeight * $swappedWidth) / ($swappedHeight ?: 1);
        if ($selectionWidth > $imageWidth) {
            $selectionWidth = $imageWidth;
            $x = 0;
        } else {
            $x = ($imageWidth - $selectionWidth) / 2;
        }

        $selectionHeight = ($swappedHeight * $selectionWidth) / ($swappedWidth ?: 1);
        if ($selectionHeight > $imageHeight) {
            $selectionHeight = $imageHeight;
            $y = 0;
        } else {
            $y = ($imageHeight - $selectionHeight) / 2;
        }

        $x2 = $selectionWidth + $x;
        $y2 = $selectionHeight + $y;

        $ratio = $swappedHeight > 0 ? $swappedWidth / $swappedHeight : 1;

        return [
            'w' => (int) round($imageWidth),
            'h' => (int) round($imageHeight),
            'x' => (int) round($x),
            'y' => (int) round($y),
            'x2' => (int) round($x2),
            'y2' => (int) round($y2),
            'ratio' => $ratio,
            'printW' => $swappedWidth,
            'printH' => $swappedHeight,
            'orientation' => $orientation,
            'shape' => $shape,
        ];
    }

    /**
     * Normalise print measurements into centimetres.
     *
     * @param mixed $value
     * @param float|null $dpi
     *
     * @return float
     */
    private function normaliseMeasurement($value, $dpi = null)
    {
        $unit = null;

        if (is_string($value)) {
            $normalized = trim(str_replace(',', '.', $value));
            if (stripos($normalized, 'cm') !== false) {
                $unit = 'cm';
                $normalized = str_ireplace('cm', '', $normalized);
            } elseif (stripos($normalized, 'mm') !== false) {
                $unit = 'mm';
                $normalized = str_ireplace('mm', '', $normalized);
            } elseif (preg_match('/inch|inches|\bin\b/i', $normalized)) {
                $unit = 'in';
                $normalized = preg_replace('/inch|inches|\bin\b/i', '', $normalized);
            }

            if (preg_match('/[-+]?[0-9]*\.?[0-9]+/', $normalized, $matches)) {
                $value = (float) $matches[0];
            } else {
                $value = 0.0;
            }
        }

        if (!is_numeric($value)) {
            return 0.0;
        }

        $number = (float) $value;

        if ($unit === 'mm') {
            return $number / 10;
        }

        if ($unit === 'in') {
            return $number * 2.54;
        }

        if ($unit === 'cm') {
            return $number;
        }

        if ($dpi && $dpi > 0 && $number > 100) {
            return ($number / $dpi) * 2.54;
        }

        if ($number > 1000) {
            return $number / 10;
        }

        return $number;
    }
}
