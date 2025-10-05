<?php

class FotoartpuzzlePreviewModuleFrontController extends ModuleFrontController
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
            $file = Tools::getValue('file');
            $text = $this->sanitizeBoxText(Tools::getValue('box_text', ''));
            $color = Tools::getValue('box_color', '#FFFFFF');
            $font = (string) Tools::getValue('box_font', 'Roboto');

            $this->validateBoxOptions($text, $color, $font);

            if (!$file || !file_exists($file)) {
                throw new Exception($this->module->l('File not found.'));
            }

            $previewPath = $this->generatePreview($file, $text, $color, $font);

            return [
                'success' => true,
                'preview' => $previewPath,
                'download_url' => $this->module->getDownloadLink($previewPath, 'front', ['disposition' => 'inline']),
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
        if (!$token || $token !== $this->module->getFrontToken('preview')) {
            throw new Exception($this->module->l('Invalid token.'));
        }
    }

    private function generatePreview($file, $text, $color, $font)
    {
        $cart = $this->context->cart;
        if (!$cart || !$cart->id) {
            throw new Exception($this->module->l('Cart not available.'));
        }

        $previewDir = FAPPathBuilder::getPreviewPath();
        if (!is_dir($previewDir)) {
            @mkdir($previewDir, 0750, true);
        }

        $destination = $previewDir . '/' . (int) $cart->id . '_' . sha1($file . microtime(true)) . '.jpg';
        $renderer = new FAPBoxRenderer();
        $renderer->render($file, $text, $color, $font, $destination);

        return $destination;
    }

    private function sanitizeBoxText($text)
    {
        $text = trim((string) $text);
        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return $text;
    }

    private function validateBoxOptions($text, &$color, &$font)
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
}
