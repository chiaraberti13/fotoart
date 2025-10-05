<?php

class AdminFotoArtPuzzleController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->list_no_link = true;
    }

    public function initContent()
    {
        parent::initContent();
        $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/production.tpl');
        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }
}
