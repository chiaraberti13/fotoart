<?php
/**
 * Restituisce dati per anteprima.
 */

class PuzzlecustomizerPreviewModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        header('Content-Type: application/json');

        $this->ajaxDie(json_encode([
            'success' => true,
            'message' => $this->module->l('Anteprima generata correttamente.'),
        ]));
    }
}
