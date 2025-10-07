<?php

class FotoartpuzzleSummaryModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
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
            $cart = $this->context->cart;
            if (!$cart || !$cart->id) {
                throw new Exception($this->module->l('Cart not available.'));
            }

            $idProduct = (int) Tools::getValue('id_product');
            $idProductAttribute = (int) Tools::getValue('id_product_attribute');
            $imagePath = Tools::getValue('file');
            $boxText = $this->sanitizeBoxText(Tools::getValue('box_text', ''));
            $boxColor = (string) Tools::getValue('box_color', '#FFFFFF');
            $boxFont = (string) Tools::getValue('box_font', 'Roboto');
            $format = Tools::getValue('format');
            $formatId = (int) Tools::getValue('format_id');
            $formatReference = Tools::getValue('format_reference', '');
            $boxId = (int) Tools::getValue('box_id');
            $boxReference = Tools::getValue('box_reference', '');
            $boxName = Tools::getValue('box_name', '');
            $previewPath = (string) Tools::getValue('preview_path', '');
            $previewUrl = (string) Tools::getValue('preview_url', '');
            $thumbnailPath = (string) Tools::getValue('thumbnail_path', '');
            $thumbnailUrl = (string) Tools::getValue('thumbnail_url', '');
            $downloadUrl = (string) Tools::getValue('download_url', '');
            $orientation = (string) Tools::getValue('orientation', '');
            $imageWidth = (int) Tools::getValue('image_width');
            $imageHeight = (int) Tools::getValue('image_height');
            $printable = Tools::getValue('printable', null);
            $qualityScore = Tools::getValue('quality', null);
            $pieces = Tools::getValue('pieces', null);
            $coordinatesPayload = $this->decodeJsonField('coordinates');
            $formatPayload = $this->decodeJsonField('format_payload');
            $formatDetailsPayload = $this->decodeJsonField('format_details');
            $boxPayload = $this->decodeJsonField('box_payload');
            $cropPayload = $this->decodeJsonField('crop');
            $pdfNote = $this->sanitizeNote(Tools::getValue('pdf_note', ''));
            $downloadNote = $this->sanitizeNote(Tools::getValue('download_note', ''));

            if (!$idProduct || !$imagePath || !file_exists($imagePath)) {
                throw new Exception($this->module->l('Missing data to create customization.'));
            }

            if (!FAPConfiguration::isProductEnabled($idProduct)) {
                throw new Exception($this->module->l('This product is not available for customization.'));
            }

            $this->validateBoxOptions($boxText, $boxColor, $boxFont);
            $selectedFormat = $this->validateFormat($format, $imagePath, $formatId);

            $metadata = [
                'color' => $boxColor,
                'font' => $boxFont,
                'format' => is_array($selectedFormat) && isset($selectedFormat['name']) ? $selectedFormat['name'] : (string) $format,
            ];

            if ($formatId) {
                $metadata['format_id'] = (int) $formatId;
            }

            if ($formatReference !== '') {
                $metadata['format_reference'] = (string) $formatReference;
            }

            if ($boxId) {
                $metadata['box_id'] = (int) $boxId;
            }

            if ($boxReference !== '') {
                $metadata['box_reference'] = (string) $boxReference;
            }

            if ($boxName !== '') {
                $metadata['box_name'] = (string) $boxName;
            }

            if (is_array($selectedFormat)) {
                $metadata['format_details'] = $selectedFormat;
            }

            if ($previewPath && file_exists($previewPath)) {
                $metadata['preview_path'] = $previewPath;
            }

            if ($previewUrl !== '') {
                $metadata['preview_url'] = $previewUrl;
            }

            if ($thumbnailPath !== '' && file_exists($thumbnailPath)) {
                $metadata['thumbnail_path'] = $thumbnailPath;
            }

            if ($thumbnailUrl !== '') {
                $metadata['thumbnail_url'] = $thumbnailUrl;
            }

            if ($downloadUrl !== '') {
                $metadata['download_url'] = $downloadUrl;
            }

            if ($orientation !== '') {
                $metadata['orientation'] = $orientation;
            }

            if ($imageWidth > 0) {
                $metadata['image_width'] = $imageWidth;
            }

            if ($imageHeight > 0) {
                $metadata['image_height'] = $imageHeight;
            }

            if ($pieces !== null && $pieces !== '') {
                $metadata['pieces'] = (int) $pieces;
            }

            if ($printable !== null && $printable !== '') {
                $metadata['printable'] = (bool) (int) $printable;
            }

            if ($qualityScore !== null && $qualityScore !== '') {
                $metadata['quality'] = (int) $qualityScore;
            }

            if (!empty($coordinatesPayload)) {
                $metadata['coordinates'] = $coordinatesPayload;
            }

            if (!empty($formatPayload)) {
                $metadata['format_payload'] = $formatPayload;
            }

            if (!empty($formatDetailsPayload)) {
                $metadata['format_details'] = $formatDetailsPayload;
            }

            if (!empty($boxPayload)) {
                $metadata['box_payload'] = $boxPayload;
            }

            if (!empty($cropPayload)) {
                $metadata['crop'] = $cropPayload;
            }

            if ($pdfNote !== '') {
                $metadata['pdf_note'] = $pdfNote;
            }

            if ($downloadNote !== '') {
                $metadata['download_note'] = $downloadNote;
            }

            if (!isset($metadata['pieces']) && isset($selectedFormat['pieces'])) {
                $metadata['pieces'] = (int) $selectedFormat['pieces'];
            }

            $idCustomization = FAPCustomizationService::createCustomization($cart, $idProduct, $imagePath, $boxText, $metadata, $idProductAttribute);

            return [
                'success' => true,
                'id_customization' => $idCustomization,
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    private function validateToken()
    {
        $token = Tools::getValue('token');
        if (!$token || $token !== $this->module->getFrontToken('summary')) {
            throw new Exception($this->module->l('Invalid token.'));
        }
    }

    private function sanitizeBoxText($text)
    {
        $text = trim((string) $text);
        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return $text;
    }

    private function validateBoxOptions(&$text, &$color, &$font)
    {
        $config = FAPConfiguration::getFrontConfig();
        $maxChars = (int) $config['box']['maxChars'];
        if ($maxChars > 0 && Tools::strlen($text) > $maxChars) {
            throw new Exception(sprintf($this->module->l('The box text cannot exceed %d characters.'), $maxChars));
        }

        $allowedColors = array_map([$this, 'normalizeColor'], $config['box']['colors']);
        $normalizedColor = $this->normalizeColor($color);
        if (!in_array($normalizedColor, $allowedColors, true)) {
            throw new Exception($this->module->l('Selected color is not available.'));
        }

        $allowedFonts = array_map(function ($font) {
            if (is_array($font) && isset($font['name'])) {
                return trim((string) $font['name']);
            }

            return trim((string) $font);
        }, $config['box']['fonts']);
        $normalizedFont = trim((string) $font);
        if (!in_array($normalizedFont, $allowedFonts, true)) {
            throw new Exception($this->module->l('Selected font is not available.'));
        }

        $color = $normalizedColor;
        $font = $normalizedFont;
    }

    private function normalizeColor($color)
    {
        $color = trim((string) $color);
        if ($color === '') {
            return $color;
        }

        if ($color[0] !== '#') {
            $color = '#' . $color;
        }

        return '#' . Tools::strtoupper(ltrim($color, '#'));
    }

    private function decodeJsonField($name)
    {
        $value = Tools::getValue($name);
        if (!is_string($value) || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return null;
    }

    private function sanitizeNote($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = strip_tags($value);
        $value = preg_replace('/[\r\n]+/', "\n", $value);

        return Tools::substr($value, 0, 1000);
    }
    private function validateFormat($format, $imagePath, $formatId = null)
    {
        $config = FAPConfiguration::getFrontConfig();
        $formats = $config['formats'];
        $selected = null;

        foreach ($formats as $candidate) {
            if (is_array($candidate)) {
                if ($formatId && isset($candidate['id']) && (int) $candidate['id'] === (int) $formatId) {
                    $selected = $candidate;
                    break;
                }

                if (isset($candidate['name']) && $candidate['name'] === $format) {
                    $selected = $candidate;
                    break;
                }

                if (isset($candidate['id']) && (string) $candidate['id'] === (string) $format) {
                    $selected = $candidate;
                    break;
                }
            }

            if ($candidate === $format) {
                $selected = ['name' => $candidate];
                break;
            }
        }

        if (!$selected) {
            throw new Exception($this->module->l('Selected format is not available.'));
        }

        $imageSize = @getimagesize($imagePath);
        if (!$imageSize) {
            throw new Exception($this->module->l('Unable to read the uploaded image.'));
        }

        $requiredWidth = isset($selected['width']) ? (int) $selected['width'] : 0;
        $requiredHeight = isset($selected['height']) ? (int) $selected['height'] : 0;
        if (($requiredWidth > 0 && $imageSize[0] < $requiredWidth) || ($requiredHeight > 0 && $imageSize[1] < $requiredHeight)) {
            throw new Exception($this->module->l('The uploaded image is too small for the selected format.'));
        }

        if (isset($selected['dpi']) && (int) $selected['dpi'] > 0 && $requiredWidth > 0 && $requiredHeight > 0) {
            $requiredDpi = (int) $selected['dpi'];
            $requiredWidthInches = $requiredWidth / $requiredDpi;
            $requiredHeightInches = $requiredHeight / $requiredDpi;
            if ($requiredWidthInches > 0 && $requiredHeightInches > 0) {
                $dpiWidth = $imageSize[0] / $requiredWidthInches;
                $dpiHeight = $imageSize[1] / $requiredHeightInches;
                $actualDpi = (int) floor(min($dpiWidth, $dpiHeight));
                if ($actualDpi < $requiredDpi) {
                    throw new Exception($this->module->l('The image resolution is not sufficient for the selected format.'));
                }
                $selected['actual_dpi'] = $actualDpi;
            }
        }

        if (is_array($selected) && !isset($selected['name'])) {
            $selected['name'] = (string) $format;
        }

        return $selected;
    }
}
