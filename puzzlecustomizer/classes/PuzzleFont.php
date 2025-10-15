<?php
/**
 * Gestione dei font caricabili.
 */

class PuzzleFont extends ObjectModel
{
    public $name;
    public $file;
    public $preview;
    public $active;

    public static $definition = [
        'table' => 'puzzle_font',
        'primary' => 'id_puzzle_font',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
            'file' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'required' => true, 'size' => 128],
            'preview' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 128],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
        ],
    ];
}
