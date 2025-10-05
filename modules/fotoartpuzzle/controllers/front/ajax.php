<?php

class FotoartpuzzleAjaxModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
    }

    public function initContent()
    {
        parent::initContent();
        $action = Tools::getValue('action');
        switch ($action) {
            case 'config':
                $this->ajaxDie(json_encode([
                    'success' => true,
                    'config' => FAPConfiguration::getFrontConfig(),
                ]));
                break;
            default:
                $this->ajaxDie(json_encode([
                    'success' => false,
                    'message' => $this->module->l('Unknown action'),
                ]));
        }
    }
}
