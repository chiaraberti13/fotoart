<?php
/**
 * Controller: art_puzzle/controllers/front/FormatController.php
 * Gestisce la selezione dei formati puzzle
 */

class ArtPuzzleFormatModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $id_product = (int) Tools::getValue('id_product');
        $box_text = Tools::getValue('box_text');

        if ($box_text) {
            $this->context->cookie->__set('art_puzzle_box_text', $box_text);
        }

        if (!class_exists('PuzzleFormatManager')) {
            require_once _PS_MODULE_DIR_ . 'art_puzzle/classes/PuzzleFormatManager.php';
        }

        try {
            $formats = PuzzleFormatManager::getAllFormats();
        } catch (Exception $e) {
            $this->errors[] = $this->module->l('Errore durante il recupero dei formati.');
            if (class_exists('ArtPuzzleLogger')) {
                ArtPuzzleLogger::log('[FORMAT] Errore getAllFormats: ' . $e->getMessage());
            }
            $formats = [];
        }

        $this->context->smarty->assign([
            'formats' => $formats,
            'id_product' => $id_product
        ]);

        $this->setTemplate('module:art_puzzle/views/templates/front/format.tpl');
    }
}
