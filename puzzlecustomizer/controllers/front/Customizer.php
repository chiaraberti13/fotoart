<?php
/**
 * Pagina principale del configuratore.
 */

class PuzzlecustomizerCustomizerModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            'module_dir' => $this->module->getPathUri(),
            'customizer_config' => [
                'upload_url' => $this->context->link->getModuleLink($this->module->name, 'upload'),
                'save_url' => $this->context->link->getModuleLink($this->module->name, 'saveconfig'),
                'preview_url' => $this->context->link->getModuleLink($this->module->name, 'preview'),
            ],
        ]);

        $this->setTemplate('module:puzzlecustomizer/views/templates/front/customizer.tpl');
    }
}
