<?php
/**
 * Palette colori per le scatole.
 */

class PuzzleBoxColor extends ObjectModel
{
    public $name;
    public $hex;
    public $active;

    public static $definition = [
        'table' => 'puzzle_box_color',
        'primary' => 'id_puzzle_box_color',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64],
            'hex' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => true, 'size' => 7],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
        ],
    ];
}
