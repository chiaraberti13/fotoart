<?php
/**
 * Associazione tra prodotto PrestaShop e configuratore puzzle.
 */

class PuzzleProduct extends ObjectModel
{
    public $id_product;
    public $active;

    public static $definition = [
        'table' => 'puzzle_product',
        'primary' => 'id_puzzle_product',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
        ],
    ];
}
