<?php
/**
 * Colori per testi.
 */

class PuzzleTextColor extends ObjectModel
{
    public $name;
    public $hex;
    public $active;

    public static $definition = [
        'table' => 'puzzle_text_color',
        'primary' => 'id_puzzle_text_color',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64],
            'hex' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => true, 'size' => 7],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
        ],
    ];
}
