<?php
/**
 * Dimensioni e tagli disponibili per i puzzle.
 */

class PuzzleOption extends ObjectModel
{
    public $name;
    public $width_mm;
    public $height_mm;
    public $pieces;
    public $price_impact;
    public $active;

    public static $definition = [
        'table' => 'puzzle_option',
        'primary' => 'id_puzzle_option',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
            'width_mm' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'height_mm' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'pieces' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'price_impact' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
        ],
    ];
}
