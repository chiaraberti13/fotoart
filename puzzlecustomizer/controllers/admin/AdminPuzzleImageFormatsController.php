<?php
require_once dirname(__DIR__, 2) . '/classes/PuzzleImageFormat.php';

class AdminPuzzleImageFormatsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = PuzzleImageFormat::$definition['table'];
        $this->className = PuzzleImageFormat::class;
        $this->lang = false;
        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_puzzle_image_format' => ['title' => $this->l('ID')],
            'name' => ['title' => $this->l('Nome')],
            'extensions' => ['title' => $this->l('Estensioni')],
            'max_size' => ['title' => $this->l('Dimensione max (MB)')],
            'mime_types' => ['title' => $this->l('MIME supportati')],
            'active' => ['title' => $this->l('Attivo'), 'active' => 'status'],
        ];

        $this->fields_form = [
            'legend' => ['title' => $this->l('Formato immagine')],
            'input' => [
                ['type' => 'text', 'label' => $this->l('Nome'), 'name' => 'name', 'required' => true],
                ['type' => 'text', 'label' => $this->l('Estensioni (separate da virgola)'), 'name' => 'extensions'],
                ['type' => 'text', 'label' => $this->l('MIME types'), 'name' => 'mime_types'],
                ['type' => 'text', 'label' => $this->l('Dimensione massima (MB)'), 'name' => 'max_size'],
                ['type' => 'switch', 'label' => $this->l('Attivo'), 'name' => 'active', 'values' => [
                    ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Sì')],
                    ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                ]],
            ],
            'submit' => ['title' => $this->l('Salva')],
        ];
    }
}
