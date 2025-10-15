<?php
class AdminPuzzleConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        $this->display = 'view';
        parent::initContent();
    }

    public function renderView()
    {
        $config = [
            'PUZZLE_MAX_FILESIZE' => Configuration::get('PUZZLE_MAX_FILESIZE', 50),
            'PUZZLE_DEFAULT_DPI' => Configuration::get('PUZZLE_DEFAULT_DPI', 300),
        ];

        $this->context->smarty->assign([
            'config' => $config,
            'form_action' => self::$currentIndex . '&token=' . $this->token,
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'puzzlecustomizer/views/templates/admin/configuration.tpl'
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitPuzzleConfiguration')) {
            Configuration::updateValue('PUZZLE_MAX_FILESIZE', (int) Tools::getValue('PUZZLE_MAX_FILESIZE'));
            Configuration::updateValue('PUZZLE_DEFAULT_DPI', (int) Tools::getValue('PUZZLE_DEFAULT_DPI'));

            $this->confirmations[] = $this->module->l('Configurazione aggiornata con successo.');
        }

        parent::postProcess();
    }
}
