<?php
require_once dirname(__DIR__, 2) . '/classes/PuzzleProduct.php';

class AdminPuzzleProductsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = PuzzleProduct::$definition['table'];
        $this->className = PuzzleProduct::class;
        $this->lang = false;
        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_puzzle_product' => ['title' => $this->l('ID'), 'align' => 'center'],
            'id_product' => ['title' => $this->l('ID Prodotto')],
            'active' => ['title' => $this->l('Attivo'), 'active' => 'status'],
        ];

        $this->fields_form = [
            'legend' => ['title' => $this->l('Prodotto puzzle')],
            'input' => [
                ['type' => 'text', 'label' => $this->l('ID Prodotto'), 'name' => 'id_product', 'required' => true],
                ['type' => 'switch', 'label' => $this->l('Attivo'), 'name' => 'active', 'values' => [
                    ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Sì')],
                    ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                ]],
            ],
            'submit' => ['title' => $this->l('Salva')],
        ];
    }
}
