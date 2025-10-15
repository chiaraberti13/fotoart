<?php
require_once dirname(__DIR__, 2) . '/classes/PuzzleFont.php';

class AdminPuzzleFontsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = PuzzleFont::$definition['table'];
        $this->className = PuzzleFont::class;
        $this->lang = false;
        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_puzzle_font' => ['title' => $this->l('ID')],
            'name' => ['title' => $this->l('Nome')],
            'file' => ['title' => $this->l('File')],
            'active' => ['title' => $this->l('Attivo'), 'active' => 'status'],
        ];

        $this->fields_form = [
            'legend' => ['title' => $this->l('Font personalizzato')],
            'input' => [
                ['type' => 'text', 'label' => $this->l('Nome'), 'name' => 'name', 'required' => true],
                ['type' => 'text', 'label' => $this->l('File'), 'name' => 'file', 'required' => true],
                ['type' => 'text', 'label' => $this->l('Preview'), 'name' => 'preview'],
                ['type' => 'switch', 'label' => $this->l('Attivo'), 'name' => 'active', 'values' => [
                    ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Sì')],
                    ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                ]],
            ],
            'submit' => ['title' => $this->l('Salva')],
        ];
    }
}
