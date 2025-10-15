<?php
class PuzzleCustomizerSqlInstall
{
    public static function install()
    {
        $queries = [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "puzzle_product` (\n                `id_puzzle_product` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n                `id_product` INT UNSIGNED NOT NULL,\n                `active` TINYINT(1) NOT NULL DEFAULT 1,\n                PRIMARY KEY (`id_puzzle_product`),\n                UNIQUE KEY `idx_product` (`id_product`)\n            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "puzzle_option` (\n                `id_puzzle_option` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n                `name` VARCHAR(128) NOT NULL,\n                `width_mm` DECIMAL(10,2) NULL,\n                `height_mm` DECIMAL(10,2) NULL,\n                `pieces` INT NULL,\n                `price_impact` DECIMAL(20,6) NULL,\n                `active` TINYINT(1) NOT NULL DEFAULT 1,\n                PRIMARY KEY (`id_puzzle_option`)\n            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "puzzle_font` (\n                `id_puzzle_font` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n                `name` VARCHAR(128) NOT NULL,\n                `file` VARCHAR(128) NOT NULL,\n                `preview` VARCHAR(128) NULL,\n                `active` TINYINT(1) NOT NULL DEFAULT 1,\n                PRIMARY KEY (`id_puzzle_font`)\n            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "puzzle_image_format` (\n                `id_puzzle_image_format` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n                `name` VARCHAR(64) NOT NULL,\n                `extensions` VARCHAR(255) NULL,\n                `max_size` INT NULL,\n                `mime_types` VARCHAR(255) NULL,\n                `active` TINYINT(1) NOT NULL DEFAULT 1,\n                PRIMARY KEY (`id_puzzle_image_format`)\n            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "puzzle_box_color` (\n                `id_puzzle_box_color` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n                `name` VARCHAR(64) NOT NULL,\n                `hex` VARCHAR(7) NOT NULL,\n                `active` TINYINT(1) NOT NULL DEFAULT 1,\n                PRIMARY KEY (`id_puzzle_box_color`)\n            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "puzzle_text_color` (\n                `id_puzzle_text_color` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n                `name` VARCHAR(64) NOT NULL,\n                `hex` VARCHAR(7) NOT NULL,\n                `active` TINYINT(1) NOT NULL DEFAULT 1,\n                PRIMARY KEY (`id_puzzle_text_color`)\n            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "puzzle_customization` (\n                `id_puzzle_customization` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n                `id_cart` INT UNSIGNED NULL,\n                `id_order` INT UNSIGNED NULL,\n                `token` VARCHAR(64) NOT NULL,\n                `configuration` LONGTEXT NULL,\n                `image_path` VARCHAR(255) NULL,\n                `status` VARCHAR(32) NULL,\n                `created_at` DATETIME NULL,\n                `updated_at` DATETIME NULL,\n                PRIMARY KEY (`id_puzzle_customization`)\n            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
        ];

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
