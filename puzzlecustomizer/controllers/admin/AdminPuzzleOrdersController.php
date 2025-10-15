<?php
require_once dirname(__DIR__, 2) . '/classes/PuzzleCustomization.php';

class AdminPuzzleOrdersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = PuzzleCustomization::$definition['table'];
        $this->className = PuzzleCustomization::class;
        $this->lang = false;
        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_puzzle_customization' => ['title' => $this->l('ID')],
            'id_cart' => ['title' => $this->l('Carrello')],
            'id_order' => ['title' => $this->l('Ordine')],
            'status' => ['title' => $this->l('Stato')],
            'created_at' => ['title' => $this->l('Creato il')],
        ];

        $this->fields_form = [
            'legend' => ['title' => $this->l('Dettaglio personalizzazione')],
            'input' => [
                ['type' => 'text', 'label' => $this->l('Token'), 'name' => 'token', 'readonly' => true],
                ['type' => 'textarea', 'label' => $this->l('Configurazione'), 'name' => 'configuration', 'autoload_rte' => false],
                ['type' => 'text', 'label' => $this->l('Percorso immagine'), 'name' => 'image_path', 'readonly' => true],
                ['type' => 'text', 'label' => $this->l('Stato'), 'name' => 'status'],
            ],
            'submit' => ['title' => $this->l('Salva')],
        ];
    }
}
