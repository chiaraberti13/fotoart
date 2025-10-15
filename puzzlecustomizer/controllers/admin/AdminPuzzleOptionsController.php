<?php
require_once dirname(__DIR__, 2) . '/classes/PuzzleOption.php';

class AdminPuzzleOptionsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = PuzzleOption::$definition['table'];
        $this->className = PuzzleOption::class;
        $this->lang = false;
        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_puzzle_option' => ['title' => $this->l('ID')],
            'name' => ['title' => $this->l('Nome')],
            'width_mm' => ['title' => $this->l('Larghezza (mm)')],
            'height_mm' => ['title' => $this->l('Altezza (mm)')],
            'pieces' => ['title' => $this->l('Pezzi')],
            'price_impact' => ['title' => $this->l('Impatto prezzo')],
            'active' => ['title' => $this->l('Attivo'), 'active' => 'status'],
        ];

        $this->fields_form = [
            'legend' => ['title' => $this->l('Opzione puzzle')],
            'input' => [
                ['type' => 'text', 'label' => $this->l('Nome'), 'name' => 'name', 'required' => true],
                ['type' => 'text', 'label' => $this->l('Larghezza (mm)'), 'name' => 'width_mm'],
                ['type' => 'text', 'label' => $this->l('Altezza (mm)'), 'name' => 'height_mm'],
                ['type' => 'text', 'label' => $this->l('Pezzi'), 'name' => 'pieces'],
                ['type' => 'text', 'label' => $this->l('Impatto prezzo'), 'name' => 'price_impact'],
                ['type' => 'switch', 'label' => $this->l('Attivo'), 'name' => 'active', 'values' => [
                    ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Sì')],
                    ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                ]],
            ],
            'submit' => ['title' => $this->l('Salva')],
        ];
    }
}
