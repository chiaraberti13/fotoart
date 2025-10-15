<?php
/**
 * Modello per le personalizzazioni dei puzzle.
 */

class PuzzleCustomization extends ObjectModel
{
    public $id_cart;
    public $id_order;
    public $token;
    public $configuration;
    public $image_path;
    public $status;
    public $created_at;
    public $updated_at;

    public static $definition = [
        'table' => 'puzzle_customization',
        'primary' => 'id_puzzle_customization',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'token' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 64],
            'configuration' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
            'image_path' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],
            'status' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 32],
            'created_at' => ['type' => self::TYPE_DATE],
            'updated_at' => ['type' => self::TYPE_DATE],
        ],
    ];
}
