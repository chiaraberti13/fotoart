<?php
/**
 * Controller: art_puzzle/controllers/front/LandingController.php
 * Mostra la pagina iniziale del modulo Art Puzzle
 */

class ArtPuzzleLandingModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Recupera ID prodotto se presente in query
        $id_product = (int) Tools::getValue('id_product');

        // Assegna i dati alla view
        $this->context->smarty->assign([
            'upload_url' => $this->context->link->getModuleLink('art_puzzle', 'upload', ['id_product' => $id_product]),
            'id_product' => $id_product
        ]);

        $this->setTemplate('module:art_puzzle/views/templates/front/landing.tpl');
    }
}
