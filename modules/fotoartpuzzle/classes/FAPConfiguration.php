<?php

require_once __DIR__ . '/FAPPuzzleRepository.php';
require_once __DIR__ . '/FAPFontManager.php';

class FAPConfiguration
{
    public const MAX_UPLOAD_SIZE = 'FAP_MAX_UPLOAD_SIZE';
    public const MIN_WIDTH = 'FAP_MIN_WIDTH';
    public const MIN_HEIGHT = 'FAP_MIN_HEIGHT';
    public const ALLOWED_EXTENSIONS = 'FAP_ALLOWED_EXTENSIONS';
    public const FORMATS = 'FAP_FORMATS';
    public const UPLOAD_FOLDER = 'FAP_UPLOAD_FOLDER';
    public const BOX_MAX_CHARS = 'FAP_BOX_MAX_CHARS';
    public const BOX_DEFAULT_TEXT = 'FAP_BOX_DEFAULT_TEXT';
    public const BOX_COLOR = 'FAP_BOX_COLOR';
    public const BOX_TEXT_COLOR = 'FAP_BOX_TEXT_COLOR';
    public const BOX_COLOR_COMBINATIONS = 'FAP_BOX_COLOR_COMBINATIONS';
    public const CUSTOM_FONTS = 'FAP_CUSTOM_FONTS';
    public const EMAIL_PREVIEW_USER = 'FAP_EMAIL_PREVIEW_USER';
    public const EMAIL_PREVIEW_ADMIN = 'FAP_EMAIL_PREVIEW_ADMIN';
    public const EMAIL_ADMIN_RECIPIENTS = 'FAP_EMAIL_ADMIN_RECIPIENTS';
    public const ENABLE_PDF_USER = 'FAP_ENABLE_PDF_USER';
    public const ENABLE_PDF_ADMIN = 'FAP_ENABLE_PDF_ADMIN';
    public const ADMIN_DOWNLOAD_SECRET = 'FAP_ADMIN_DOWNLOAD_SECRET';
    public const ENABLE_ORIENTATION = 'FAP_ENABLE_ORIENTATION';
    public const ENABLE_INTERACTIVE_CROP = 'FAP_ENABLE_INTERACTIVE_CROP';
    public const TEMP_TTL_HOURS = 'FAP_TEMP_TTL_HOURS';
    public const ANONYMIZE_FILENAMES = 'FAP_ANONYMIZE_FILENAMES';
    public const LOG_LEVEL = 'FAP_LOG_LEVEL';
    public const PUZZLE_PRODUCTS = 'FAP_PUZZLE_PRODUCTS';
    public const PUZZLE_LEGACY_MAP = 'FAP_PUZZLE_LEGACY_MAP';

    /**
     * Install default configuration values
     *
     * @return bool
     */
    public static function installDefaults()
    {
        $defaults = [
            self::MAX_UPLOAD_SIZE => 25,
            self::MIN_WIDTH => 3000,
            self::MIN_HEIGHT => 2000,
            self::ALLOWED_EXTENSIONS => 'jpg,jpeg,png',
            self::FORMATS => json_encode([
                ['name' => '500 pezzi', 'pieces' => 500, 'width' => 5000, 'height' => 3500, 'dpi' => 300],
                ['name' => '1000 pezzi', 'pieces' => 1000, 'width' => 6600, 'height' => 4800, 'dpi' => 300],
            ]),
            self::UPLOAD_FOLDER => '/upload/',
            self::BOX_MAX_CHARS => 32,
            self::BOX_DEFAULT_TEXT => 'Il mio puzzle',
            self::BOX_COLOR => '#FFFFFF',
            self::BOX_TEXT_COLOR => '#000000',
            self::BOX_COLOR_COMBINATIONS => json_encode([
                ['box' => '#FFFFFF', 'text' => '#000000'],
            ]),
            self::CUSTOM_FONTS => json_encode([]),
            self::EMAIL_PREVIEW_USER => 1,
            self::EMAIL_PREVIEW_ADMIN => 1,
            self::EMAIL_ADMIN_RECIPIENTS => Configuration::get('PS_SHOP_EMAIL'),
            self::ENABLE_PDF_USER => 0,
            self::ENABLE_PDF_ADMIN => 0,
            self::ADMIN_DOWNLOAD_SECRET => Tools::passwdGen(64),
            self::ENABLE_ORIENTATION => 1,
            self::ENABLE_INTERACTIVE_CROP => 1,
            self::TEMP_TTL_HOURS => 24,
            self::ANONYMIZE_FILENAMES => 1,
            self::LOG_LEVEL => 'INFO',
            self::PUZZLE_PRODUCTS => '',
            self::PUZZLE_LEGACY_MAP => json_encode([]),
        ];

        foreach ($defaults as $key => $value) {
            if (!Configuration::updateValue($key, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove configuration
     *
     * @return bool
     */
    public static function removeDefaults()
    {
        $keys = [
            self::MAX_UPLOAD_SIZE,
            self::MIN_WIDTH,
            self::MIN_HEIGHT,
            self::ALLOWED_EXTENSIONS,
            self::FORMATS,
            self::UPLOAD_FOLDER,
            self::BOX_MAX_CHARS,
            self::BOX_DEFAULT_TEXT,
            self::BOX_COLOR,
            self::BOX_TEXT_COLOR,
            self::BOX_COLOR_COMBINATIONS,
            self::CUSTOM_FONTS,
            self::EMAIL_PREVIEW_USER,
            self::EMAIL_PREVIEW_ADMIN,
            self::EMAIL_ADMIN_RECIPIENTS,
            self::ENABLE_PDF_USER,
            self::ENABLE_PDF_ADMIN,
            self::ADMIN_DOWNLOAD_SECRET,
            self::ENABLE_ORIENTATION,
            self::ENABLE_INTERACTIVE_CROP,
            self::TEMP_TTL_HOURS,
            self::ANONYMIZE_FILENAMES,
            self::LOG_LEVEL,
            self::PUZZLE_PRODUCTS,
            self::PUZZLE_LEGACY_MAP,
        ];

        foreach ($keys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if product is enabled
     *
     * @param int $idProduct
     *
     * @return bool
     */
    public static function isProductEnabled($idProduct)
    {
        $csv = (string) Configuration::get(self::PUZZLE_PRODUCTS);
        if ($csv === '') {
            return false;
        }

        $ids = array_filter(array_map('intval', array_map('trim', explode(',', $csv))));

        return in_array((int) $idProduct, $ids, true);
    }

    /**
     * Get configuration for front-end
     *
     * @return array
     */
    public static function getFrontConfig()
    {
        $combinations = json_decode((string) Configuration::get(self::BOX_COLOR_COMBINATIONS), true) ?: [];
        $storedFonts = json_decode((string) Configuration::get(self::CUSTOM_FONTS), true) ?: [];
        $colors = [];
        foreach ($combinations as $combination) {
            if (!empty($combination['box'])) {
                $colors[] = (string) $combination['box'];
            }
        }
        $primaryColor = (string) Configuration::get(self::BOX_COLOR);
        array_unshift($colors, $primaryColor);
        $colors = array_values(array_unique(array_map('strtoupper', array_map('trim', $colors))));

        $repository = new FAPPuzzleRepository();
        $formats = $repository->getFormats(true);
        if (empty($formats)) {
            $formats = json_decode((string) Configuration::get(self::FORMATS), true) ?: [];
        }

        $boxes = $repository->getBoxes(true);

        $fontManager = new FAPFontManager();
        $availableFonts = $fontManager->getAvailableFonts();
        $fonts = self::filterConfiguredFonts($storedFonts, $availableFonts);

        return [
            'maxUploadMb' => (int) Configuration::get(self::MAX_UPLOAD_SIZE),
            'minWidth' => (int) Configuration::get(self::MIN_WIDTH),
            'minHeight' => (int) Configuration::get(self::MIN_HEIGHT),
            'extensions' => explode(',', (string) Configuration::get(self::ALLOWED_EXTENSIONS)),
            'formats' => $formats,
            'uploadFolder' => (string) Configuration::get(self::UPLOAD_FOLDER),
            'features' => [
                'enableOrientation' => (bool) Configuration::get(self::ENABLE_ORIENTATION),
                'enableInteractiveCrop' => (bool) Configuration::get(self::ENABLE_INTERACTIVE_CROP),
            ],
            'box' => [
                'maxChars' => (int) Configuration::get(self::BOX_MAX_CHARS),
                'defaultText' => (string) Configuration::get(self::BOX_DEFAULT_TEXT),
                'color' => $primaryColor,
                'textColor' => (string) Configuration::get(self::BOX_TEXT_COLOR),
                'colors' => $colors,
                'combinations' => $combinations,
                'fonts' => $fonts,
                'uppercase' => false,
            ],
            'puzzles' => $formats,
            'boxes' => $boxes,
            'legacyMappings' => self::getLegacyMappings(),
        ];
    }

    /**
     * Retrieve the legacy mapping configuration.
     *
     * @return array
     */
    public static function getLegacyMappings()
    {
        $raw = (string) Configuration::get(self::PUZZLE_LEGACY_MAP);
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        $sanitized = [];
        foreach ($decoded as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $idProduct = isset($entry['id_product']) ? (int) $entry['id_product'] : 0;
            $legacyCode = isset($entry['legacy_code']) ? trim((string) $entry['legacy_code']) : '';
            if ($idProduct <= 0 || $legacyCode === '') {
                continue;
            }

            $sanitized[] = [
                'id_product' => $idProduct,
                'id_product_attribute' => isset($entry['id_product_attribute']) ? (int) $entry['id_product_attribute'] : 0,
                'legacy_code' => $legacyCode,
                'pieces' => isset($entry['pieces']) ? (int) $entry['pieces'] : null,
                'width_mm' => isset($entry['width_mm']) ? (int) $entry['width_mm'] : null,
                'height_mm' => isset($entry['height_mm']) ? (int) $entry['height_mm'] : null,
                'price' => isset($entry['price']) ? (float) $entry['price'] : null,
                'available' => !empty($entry['available']),
            ];
        }

        return $sanitized;
    }

    /**
     * Filter configured fonts against the filesystem.
     *
     * @param array $storedFonts
     * @param array $availableFonts
     *
     * @return array
     */
    private static function filterConfiguredFonts(array $storedFonts, array $availableFonts)
    {
        if (!$availableFonts) {
            return [];
        }

        $byName = [];
        foreach ($availableFonts as $font) {
            $key = Tools::strtolower($font['name']);
            $font['url'] = self::buildFontUrl($font['filename']);
            $byName[$key] = $font;
        }

        $selected = [];
        foreach ($storedFonts as $font) {
            if (is_array($font) && isset($font['name'])) {
                $name = Tools::strtolower($font['name']);
            } else {
                $name = Tools::strtolower((string) $font);
            }

            if ($name && isset($byName[$name])) {
                $selected[$name] = $byName[$name];
            }
        }

        if ($selected) {
            return array_values($selected);
        }

        return array_values($byName);
    }

    /**
     * Build public URL for font file.
     *
     * @param string $filename
     *
     * @return string
     */
    private static function buildFontUrl($filename)
    {
        return _MODULE_DIR_ . FotoArtPuzzle::MODULE_NAME . '/fonts/' . rawurlencode($filename);
    }

    /**
     * Parse CSV list of product ids
     *
     * @return int[]
     */
    public static function getEnabledProductIds()
    {
        $csv = (string) Configuration::get(self::PUZZLE_PRODUCTS);
        if ($csv === '') {
            return [];
        }

        $ids = array_map('intval', array_map('trim', explode(',', $csv)));

        return array_values(array_filter($ids));
    }
}
