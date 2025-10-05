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
            $previewPath = (string) Tools::getValue('preview_path', '');

            if (!$idProduct || !$imagePath || !file_exists($imagePath)) {
                throw new Exception($this->module->l('Missing data to create customization.'));
            }

            if (!FAPConfiguration::isProductEnabled($idProduct)) {
                throw new Exception($this->module->l('This product is not available for customization.'));
            }

            $this->validateBoxOptions($boxText, $boxColor, $boxFont);
            $selectedFormat = $this->validateFormat($format, $imagePath);

            $metadata = [
                'color' => $boxColor,
                'font' => $boxFont,
                'format' => is_array($selectedFormat) && isset($selectedFormat['name']) ? $selectedFormat['name'] : (string) $format,
            ];

            if (is_array($selectedFormat)) {
                $metadata['format_details'] = $selectedFormat;
            }

            if ($previewPath && file_exists($previewPath)) {
                $metadata['preview_path'] = $previewPath;
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

        $allowedFonts = array_map('trim', $config['box']['fonts']);
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

    private function validateFormat($format, $imagePath)
    {
        $config = FAPConfiguration::getFrontConfig();
        $formats = $config['formats'];
        $selected = null;

        foreach ($formats as $candidate) {
            if ((is_array($candidate) && ((isset($candidate['name']) && $candidate['name'] === $format) || (isset($candidate['id']) && (string) $candidate['id'] === (string) $format)))) {
                $selected = $candidate;
                break;
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
