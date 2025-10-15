<?php

class PuzzleCustomizerSqlInstall
{
    public static function install()
    {
        $sql = [];

        // Puzzle Products Table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'puzzle_product` (
            `id_puzzle_product` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product` INT UNSIGNED NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_puzzle_product`),
            UNIQUE KEY `idx_product` (`id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Puzzle Options Table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'puzzle_option` (
            `id_puzzle_option` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(128) NOT NULL,
            `width_mm` DECIMAL(10,2) NULL,
            `height_mm` DECIMAL(10,2) NULL,
            `pieces` INT NULL,
            `price_impact` DECIMAL(20,6) NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_puzzle_option`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Puzzle Fonts Table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'puzzle_font` (
            `id_puzzle_font` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(128) NOT NULL,
            `file` VARCHAR(128) NOT NULL,
            `preview` VARCHAR(128) NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_puzzle_font`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Puzzle Image Formats Table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'puzzle_image_format` (
            `id_puzzle_image_format` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(64) NOT NULL,
            `extensions` VARCHAR(255) NULL,
            `max_size` INT NULL,
            `mime_types` VARCHAR(255) NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_puzzle_image_format`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Puzzle Box Colors Table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'puzzle_box_color` (
            `id_puzzle_box_color` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(64) NOT NULL,
            `hex` VARCHAR(7) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_puzzle_box_color`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Puzzle Text Colors Table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'puzzle_text_color` (
            `id_puzzle_text_color` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(64) NOT NULL,
            `hex` VARCHAR(7) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_puzzle_text_color`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Puzzle Customization Table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'puzzle_customization` (
            `id_puzzle_customization` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_cart` INT UNSIGNED NULL,
            `id_order` INT UNSIGNED NULL,
            `token` VARCHAR(64) NOT NULL,
            `configuration` LONGTEXT NULL,
            `image_path` VARCHAR(255) NULL,
            `status` VARCHAR(32) NULL,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id_puzzle_customization`),
            KEY `idx_cart` (`id_cart`),
            KEY `idx_order` (`id_order`),
            KEY `idx_token` (`token`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Execute all queries
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                PrestaShopLogger::addLog(
                    'Puzzle Customizer Install Error: ' . Db::getInstance()->getMsgError() . ' | Query: ' . $query,
                    3,
                    null,
                    'PuzzleCustomizer'
                );

                return false;
            }
        }

        // Insert default data
        return self::insertDefaultData();
    }

    protected static function insertDefaultData()
    {
        // Insert default image formats
        $formats = [
            ['name' => 'JPEG', 'extensions' => 'jpg,jpeg', 'mime_types' => 'image/jpeg,image/jpg', 'max_size' => 50, 'active' => 1],
            ['name' => 'PNG', 'extensions' => 'png', 'mime_types' => 'image/png', 'max_size' => 50, 'active' => 1],
            ['name' => 'WEBP', 'extensions' => 'webp', 'mime_types' => 'image/webp', 'max_size' => 50, 'active' => 1],
            ['name' => 'TIFF', 'extensions' => 'tif,tiff', 'mime_types' => 'image/tiff,image/tif', 'max_size' => 100, 'active' => 1],
        ];

        foreach ($formats as $format) {
            Db::getInstance()->insert('puzzle_image_format', $format);
        }

        // Insert default puzzle options
        $options = [
            ['name' => '500 pieces - 40x30cm', 'width_mm' => 400, 'height_mm' => 300, 'pieces' => 500, 'price_impact' => 0, 'active' => 1],
            ['name' => '1000 pieces - 70x50cm', 'width_mm' => 700, 'height_mm' => 500, 'pieces' => 1000, 'price_impact' => 5.00, 'active' => 1],
            ['name' => '1500 pieces - 80x60cm', 'width_mm' => 800, 'height_mm' => 600, 'pieces' => 1500, 'price_impact' => 10.00, 'active' => 1],
        ];

        foreach ($options as $option) {
            Db::getInstance()->insert('puzzle_option', $option);
        }

        // Insert default colors
        $boxColors = [
            ['name' => 'White', 'hex' => '#FFFFFF', 'active' => 1],
            ['name' => 'Black', 'hex' => '#000000', 'active' => 1],
            ['name' => 'Blue', 'hex' => '#0066CC', 'active' => 1],
            ['name' => 'Red', 'hex' => '#CC0000', 'active' => 1],
        ];

        foreach ($boxColors as $color) {
            Db::getInstance()->insert('puzzle_box_color', $color);
        }

        $textColors = [
            ['name' => 'Black', 'hex' => '#000000', 'active' => 1],
            ['name' => 'White', 'hex' => '#FFFFFF', 'active' => 1],
            ['name' => 'Gold', 'hex' => '#FFD700', 'active' => 1],
            ['name' => 'Silver', 'hex' => '#C0C0C0', 'active' => 1],
        ];

        foreach ($textColors as $color) {
            Db::getInstance()->insert('puzzle_text_color', $color);
        }

        return true;
    }
}
