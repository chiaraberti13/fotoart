<?php

class FAPPathBuilder
{
    /**
     * Ensure module filesystem structure exists
     *
     * @return bool
     */
    public static function ensureFilesystem()
    {
        $paths = [
            self::getBasePath(),
            self::getTempPath(),
            self::getOrdersPath(),
            self::getPreviewPath(),
            self::getLogPath(),
        ];

        foreach ($paths as $path) {
            if (!self::createDirectory($path)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Base storage path
     *
     * @return string
     */
    public static function getBasePath()
    {
        return _PS_MODULE_DIR_ . FotoArtPuzzle::MODULE_NAME . '/var';
    }

    public static function getTempPath()
    {
        return self::getBasePath() . '/tmp';
    }

    public static function getPreviewPath()
    {
        return self::getBasePath() . '/previews';
    }

    public static function getOrdersPath()
    {
        return self::getBasePath() . '/orders';
    }

    public static function getLogPath()
    {
        return self::getBasePath() . '/logs';
    }

    /**
     * Builds path for cart specific folder
     *
     * @param int $idCart
     *
     * @return string
     */
    public static function getCartPath($idCart)
    {
        return self::getTempPath() . '/cart_' . (int) $idCart;
    }

    /**
     * Builds path for order storage
     *
     * @param int $idOrder
     *
     * @return string
     */
    public static function getOrderPath($idOrder)
    {
        return self::getOrdersPath() . '/' . (int) $idOrder;
    }

    /**
     * Create directory if missing
     *
     * @param string $path
     *
     * @return bool
     */
    private static function createDirectory($path)
    {
        if (is_dir($path)) {
            return true;
        }

        return @mkdir($path, 0750, true);
    }
}
