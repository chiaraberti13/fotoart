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
        $evaluations = $this->qualityService->evaluateFormats($width, $height, $formats);

        return [
            'orientation' => $width >= $height ? 'landscape' : 'portrait',
            'width' => (int) $width,
            'height' => (int) $height,
            'formats' => $evaluations,
            'printable' => $this->qualityService->hasPrintableFormat($evaluations),
        ];
    }
}
