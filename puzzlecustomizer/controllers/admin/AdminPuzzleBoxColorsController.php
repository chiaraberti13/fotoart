<?php
require_once dirname(__DIR__, 2) . '/classes/PuzzleBoxColor.php';

class AdminPuzzleBoxColorsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = PuzzleBoxColor::$definition['table'];
        $this->className = PuzzleBoxColor::class;
        $this->lang = false;
        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_puzzle_box_color' => ['title' => $this->l('ID')],
            'name' => ['title' => $this->l('Nome')],
            'hex' => ['title' => $this->l('Colore')],
            'active' => ['title' => $this->l('Attivo'), 'active' => 'status'],
        ];

        $this->fields_form = [
            'legend' => ['title' => $this->l('Colore scatola')],
            'input' => [
                ['type' => 'text', 'label' => $this->l('Nome'), 'name' => 'name', 'required' => true],
                ['type' => 'color', 'label' => $this->l('Colore'), 'name' => 'hex', 'required' => true],
                ['type' => 'switch', 'label' => $this->l('Attivo'), 'name' => 'active', 'values' => [
                    ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Sì')],
                    ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                ]],
            ],
            'submit' => ['title' => $this->l('Salva')],
        ];
    }
}
