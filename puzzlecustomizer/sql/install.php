<?php

class PuzzleCustomizerSqlInstall
{
    public static function install()
    {
        $queries = [
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%1$spuzzle_product` (
                    `id_puzzle_product` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_product` INT UNSIGNED NOT NULL,
                    `active` TINYINT(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id_puzzle_product`),
                    UNIQUE KEY `idx_product` (`id_product`)
                ) ENGINE=%2$s DEFAULT CHARSET=utf8;',
                _DB_PREFIX_,
                _MYSQL_ENGINE_
            ),
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%1$spuzzle_option` (
                    `id_puzzle_option` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(128) NOT NULL,
                    `width_mm` DECIMAL(10,2) NULL,
                    `height_mm` DECIMAL(10,2) NULL,
                    `pieces` INT NULL,
                    `price_impact` DECIMAL(20,6) NULL,
                    `active` TINYINT(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id_puzzle_option`)
                ) ENGINE=%2$s DEFAULT CHARSET=utf8;',
                _DB_PREFIX_,
                _MYSQL_ENGINE_
            ),
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%1$spuzzle_font` (
                    `id_puzzle_font` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(128) NOT NULL,
                    `file` VARCHAR(128) NOT NULL,
                    `preview` VARCHAR(128) NULL,
                    `active` TINYINT(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id_puzzle_font`)
                ) ENGINE=%2$s DEFAULT CHARSET=utf8;',
                _DB_PREFIX_,
                _MYSQL_ENGINE_
            ),
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%1$spuzzle_image_format` (
                    `id_puzzle_image_format` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(64) NOT NULL,
                    `extensions` VARCHAR(255) NULL,
                    `max_size` INT NULL,
                    `mime_types` VARCHAR(255) NULL,
                    `active` TINYINT(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id_puzzle_image_format`)
                ) ENGINE=%2$s DEFAULT CHARSET=utf8;',
                _DB_PREFIX_,
                _MYSQL_ENGINE_
            ),
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%1$spuzzle_box_color` (
                    `id_puzzle_box_color` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(64) NOT NULL,
                    `hex` VARCHAR(7) NOT NULL,
                    `active` TINYINT(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id_puzzle_box_color`)
                ) ENGINE=%2$s DEFAULT CHARSET=utf8;',
                _DB_PREFIX_,
                _MYSQL_ENGINE_
            ),
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%1$spuzzle_text_color` (
                    `id_puzzle_text_color` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(64) NOT NULL,
                    `hex` VARCHAR(7) NOT NULL,
                    `active` TINYINT(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id_puzzle_text_color`)
                ) ENGINE=%2$s DEFAULT CHARSET=utf8;',
                _DB_PREFIX_,
                _MYSQL_ENGINE_
            ),
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%1$spuzzle_customization` (
                    `id_puzzle_customization` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_cart` INT UNSIGNED NULL,
                    `id_order` INT UNSIGNED NULL,
                    `token` VARCHAR(64) NOT NULL,
                    `configuration` LONGTEXT NULL,
                    `image_path` VARCHAR(255) NULL,
                    `status` VARCHAR(32) NULL,
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id_puzzle_customization`)
                ) ENGINE=%2$s DEFAULT CHARSET=utf8;',
                _DB_PREFIX_,
                _MYSQL_ENGINE_
            ),
        ];

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
