<?php
class PuzzleCustomizerSqlUninstall
{
    public static function uninstall()
    {
        $tables = [
            'puzzle_product',
            'puzzle_option',
            'puzzle_font',
            'puzzle_image_format',
            'puzzle_box_color',
            'puzzle_text_color',
            'puzzle_customization',
        ];

        foreach ($tables as $table) {
            if (!Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . pSQL($table) . '`')) {
                return false;
            }
        }

        return true;
    }
}
