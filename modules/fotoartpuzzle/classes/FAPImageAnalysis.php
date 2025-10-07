<?php

class FAPImageAnalysis
{
    /**
     * @var FAPQualityService
     */
    private $qualityService;

    /**
     * @param FAPQualityService|null $qualityService
     */
    public function __construct(?FAPQualityService $qualityService = null)
    {
        $this->qualityService = $qualityService ?: new FAPQualityService();
    }
  
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
     * Analyse the uploaded image against configured formats
     *
     * @param int $width
     * @param int $height
     * @param array $formats
     *
     * @return array
     */
    public function analyse($width, $height, array $formats)
    {
        $orientation = $width >= $height ? 'landscape' : 'portrait';
        $evaluations = $this->qualityService->evaluateFormats($width, $height, $formats);
        $evaluations = [];

        foreach ($formats as $format) {
            $printWidth = isset($format['width']) ? (float) $format['width'] : 0.0;
            $printHeight = isset($format['height']) ? (float) $format['height'] : 0.0;

            if ($printWidth <= 0 || $printHeight <= 0) {
                continue;
            }

            $normalized = $this->normalisePrintSize($printWidth, $printHeight);

            $quality = $this->calculateQuality($normalized['width'], $normalized['height'], $width, $height);

            $evaluations[] = array_merge($format, [
                'quality' => $quality,
                'coordinates' => [
                    'landscape' => $this->buildSelection(
                        $width,
                        $height,
                        $normalized['width'],
                        $normalized['height'],
                        'landscape',
                        $format
                    ),
                    'portrait' => $this->buildSelection(
                        $width,
                        $height,
                        $normalized['height'],
                        $normalized['width'],
                        'portrait',
                        $format
                    ),
                ],
            ]);
        }

        return [
            'orientation' => $orientation,
            'width' => $width,
            'height' => $height,
            'formats' => $evaluations,
            'printable' => $this->qualityService->hasPrintableFormat($evaluations),
            'printable' => $this->hasPrintableFormat($evaluations),
        ];
    }

    /**
     * Determine if at least one format is printable
     *
     * @param array $evaluations
     *
     * @return bool
     */
    private function hasPrintableFormat(array $evaluations)
    {
        foreach ($evaluations as $evaluation) {
            if (!empty($evaluation['quality'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate quality score based on DPI
     *
     * @param float $printWidthCm
     * @param float $printHeightCm
     * @param int $pixelWidth
     * @param int $pixelHeight
     *
     * @return int
     */
    private function calculateQuality($printWidthCm, $printHeightCm, $pixelWidth, $pixelHeight)
    {
        if ($printWidthCm <= 0 || $printHeightCm <= 0 || $pixelWidth <= 0 || $pixelHeight <= 0) {
            return self::QUALITY_INSUFFICIENT;
        }

        $widthInches = $printWidthCm / 2.54;
        $heightInches = $printHeightCm / 2.54;

        if ($widthInches <= 0 || $heightInches <= 0) {
            return self::QUALITY_INSUFFICIENT;
        }

        $dpiWidth = $pixelWidth / $widthInches;
        $dpiHeight = $pixelHeight / $heightInches;

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
     * Build default selection for a given format
     *
     * @param int $imageWidth
     * @param int $imageHeight
     * @param float $printWidthCm
     * @param float $printHeightCm
     * @param string $orientation
     * @param array $format
     *
     * @return array
     */
    private function buildSelection($imageWidth, $imageHeight, $printWidthCm, $printHeightCm, $orientation, array $format)
    {
        $ratio = $printHeightCm > 0 ? $printWidthCm / $printHeightCm : 1;

        $selectionWidth = ($imageHeight * $printWidthCm) / ($printHeightCm ?: 1);
        if ($selectionWidth > $imageWidth) {
            $selectionWidth = $imageWidth;
            $x = 0;
        } else {
            $x = ($imageWidth - $selectionWidth) / 2;
        }

        $selectionHeight = ($imageWidth * $printHeightCm) / ($printWidthCm ?: 1);
        if ($selectionHeight > $imageHeight) {
            $selectionHeight = $imageHeight;
            $y = 0;
        } else {
            $y = ($imageHeight - $selectionHeight) / 2;
        }

        $x2 = $x + $selectionWidth;
        $y2 = $y + $selectionHeight;

        return [
            'w' => (int) round($imageWidth),
            'h' => (int) round($imageHeight),
            'x' => (int) round($x),
            'y' => (int) round($y),
            'x2' => (int) round($x2),
            'y2' => (int) round($y2),
            'ratio' => $ratio,
            'printW' => $printWidthCm,
            'printH' => $printHeightCm,
            'orientation' => $orientation,
            'shape' => isset($format['shape']) ? $format['shape'] : null,
        ];
    }

    /**
     * Normalise print size to centimetres
     *
     * @param float $width
     * @param float $height
     *
     * @return array
     */
    private function normalisePrintSize($width, $height)
    {
        if ($width > 100) {
            $width = $width / 100;
        }

        if ($height > 100) {
            $height = $height / 100;
        }

        return [
            'width' => $width,
            'height' => $height,
        ];
    }
}
