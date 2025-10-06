<?php
/**
 * Controller: art_puzzle/controllers/front/BoxController.php
 * Gestisce la selezione e il salvataggio della scatola personalizzata
 */

class Art_PuzzleBoxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $id_product = (int) Tools::getValue('id_product');
        $box_image = Tools::getValue('box_image');
        $box_text = Tools::getValue('box_text');

        if (!$box_image || !$box_text) {
            $this->logAndError('Dati della scatola mancanti.');
        }

        // Salva in cookie
        $this->context->cookie->__set('art_puzzle_box_image', $box_image);
        $this->context->cookie->__set('art_puzzle_box_text', $box_text);

        $this->context->smarty->assign([
            'box_image' => $box_image,
            'box_text' => $box_text,
            'id_product' => $id_product,
        ]);

        $this->setTemplate('module:art_puzzle/views/templates/front/box.tpl');
    }

    private function logAndError($message)
    {
        $this->errors[] = $this->module->l($message);
        if (class_exists('ArtPuzzleLogger')) {
            ArtPuzzleLogger::log('[BOX] ' . $message);
        }
    }
}