<?php
/**
 * Formati di immagine supportati.
 */

class PuzzleImageFormat extends ObjectModel
{
    public $name;
    public $extensions;
    public $max_size;
    public $mime_types;
    public $active;

    public static $definition = [
        'table' => 'puzzle_image_format',
        'primary' => 'id_puzzle_image_format',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64],
            'extensions' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'max_size' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'mime_types' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
        ],
    ];
}
