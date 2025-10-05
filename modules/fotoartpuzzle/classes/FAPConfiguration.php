<?php

class FAPConfiguration
{
    public const MAX_UPLOAD_SIZE = 'FAP_MAX_UPLOAD_SIZE';
    public const MIN_WIDTH = 'FAP_MIN_WIDTH';
    public const MIN_HEIGHT = 'FAP_MIN_HEIGHT';
    public const ALLOWED_EXTENSIONS = 'FAP_ALLOWED_EXTENSIONS';
    public const FORCE_REENCODE = 'FAP_FORCE_REENCODE';
    public const STRIP_EXIF = 'FAP_STRIP_EXIF';
    public const FORMATS = 'FAP_FORMATS';
    public const BOX_MAX_CHARS = 'FAP_BOX_MAX_CHARS';
    public const BOX_COLORS = 'FAP_BOX_COLORS';
    public const BOX_FONTS = 'FAP_BOX_FONTS';
    public const BOX_UPPERCASE = 'FAP_BOX_UPPERCASE';
    public const EMAIL_CLIENT = 'FAP_EMAIL_CLIENT';
    public const EMAIL_ADMIN = 'FAP_EMAIL_ADMIN';
    public const EMAIL_ADMIN_RECIPIENTS = 'FAP_EMAIL_ADMIN_RECIPIENTS';
    public const EMAIL_ATTACH_PREVIEW = 'FAP_EMAIL_ATTACH_PREVIEW';
    public const TEMP_TTL_HOURS = 'FAP_TEMP_TTL_HOURS';
    public const ANONYMIZE_FILENAMES = 'FAP_ANONYMIZE_FILENAMES';
    public const LOG_LEVEL = 'FAP_LOG_LEVEL';
    public const ENABLED_PRODUCTS = 'FAP_ENABLED_PRODUCTS';

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
            self::FORCE_REENCODE => 1,
            self::STRIP_EXIF => 1,
            self::FORMATS => json_encode([
                ['name' => '500 pezzi', 'pieces' => 500, 'width' => 5000, 'height' => 3500, 'dpi' => 300],
                ['name' => '1000 pezzi', 'pieces' => 1000, 'width' => 6600, 'height' => 4800, 'dpi' => 300],
            ]),
            self::BOX_MAX_CHARS => 32,
            self::BOX_COLORS => json_encode(['#000000', '#FFFFFF', '#D32F2F', '#1976D2']),
            self::BOX_FONTS => json_encode(['Lato', 'Montserrat', 'Roboto']),
            self::BOX_UPPERCASE => 0,
            self::EMAIL_CLIENT => 1,
            self::EMAIL_ADMIN => 1,
            self::EMAIL_ADMIN_RECIPIENTS => Configuration::get('PS_SHOP_EMAIL'),
            self::EMAIL_ATTACH_PREVIEW => 1,
            self::TEMP_TTL_HOURS => 24,
            self::ANONYMIZE_FILENAMES => 1,
            self::LOG_LEVEL => 'INFO',
            self::ENABLED_PRODUCTS => json_encode([]),
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
            self::FORCE_REENCODE,
            self::STRIP_EXIF,
            self::FORMATS,
            self::BOX_MAX_CHARS,
            self::BOX_COLORS,
            self::BOX_FONTS,
            self::BOX_UPPERCASE,
            self::EMAIL_CLIENT,
            self::EMAIL_ADMIN,
            self::EMAIL_ADMIN_RECIPIENTS,
            self::EMAIL_ATTACH_PREVIEW,
            self::TEMP_TTL_HOURS,
            self::ANONYMIZE_FILENAMES,
            self::LOG_LEVEL,
            self::ENABLED_PRODUCTS,
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
        $enabled = self::getEnabledProducts();

        return in_array((int) $idProduct, $enabled, true);
    }

    /**
     * Get configuration for front-end
     *
     * @return array
     */
    public static function getFrontConfig()
    {
        return [
            'maxUploadMb' => (int) Configuration::get(self::MAX_UPLOAD_SIZE),
            'minWidth' => (int) Configuration::get(self::MIN_WIDTH),
            'minHeight' => (int) Configuration::get(self::MIN_HEIGHT),
            'extensions' => array_filter(array_map('trim', explode(',', (string) Configuration::get(self::ALLOWED_EXTENSIONS)))),
            'formats' => json_decode((string) Configuration::get(self::FORMATS), true) ?: [],
            'box' => [
                'maxChars' => (int) Configuration::get(self::BOX_MAX_CHARS),
                'colors' => json_decode((string) Configuration::get(self::BOX_COLORS), true) ?: [],
                'fonts' => json_decode((string) Configuration::get(self::BOX_FONTS), true) ?: [],
                'uppercase' => (bool) Configuration::get(self::BOX_UPPERCASE),
            ],
        ];
    }

    /**
     * Return list of enabled product IDs
     *
     * @return int[]
     */
    public static function getEnabledProducts()
    {
        $enabled = json_decode((string) Configuration::get(self::ENABLED_PRODUCTS), true);
        if (!is_array($enabled)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $enabled)));
    }

    /**
     * Return enabled product IDs as comma separated string
     *
     * @return string
     */
    public static function getEnabledProductsString()
    {
        $ids = self::getEnabledProducts();

        return implode(',', $ids);
    }
}
