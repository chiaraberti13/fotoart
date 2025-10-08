<?php
/**
 * Controller: art_puzzle/controllers/front/CustomizerController.php
 * Gestisce la schermata di personalizzazione iniziale del puzzle
 */

class Art_puzzleCustomizerModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $id_product = (int)Tools::getValue('id_product');

        // Verifica accesso utente
        if (!$this->context->customer->isLogged() && !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
            Tools::redirect('index.php?controller=authentication&back=' . urlencode(
                $this->context->link->getModuleLink('art_puzzle', 'customizer', ['id_product' => $id_product])
            ));
            return;
        }

        // Verifica prodotto valido
        if (!$id_product || !$this->module->isPuzzleProduct($id_product)) {
            $this->logAndError('Prodotto non valido o non personalizzabile.');
            $this->redirectWithNotifications($this->context->link->getPageLink('index'));
            return;
        }

        // Carica prodotto
        $product = new Product($id_product, true, $this->context->language->id);
        if (!Validate::isLoadedObject($product)) {
            $this->logAndError('Prodotto non trovato.');
            $this->redirectWithNotifications($this->context->link->getPageLink('index'));
            return;
        }

        // Assegna dati personalizzazione
        $this->context->smarty->assign([
            'id_product' => $id_product,
            'product' => $product,
            'product_url' => $this->context->link->getProductLink($id_product),
            'upload_max_size' => Configuration::get('ART_PUZZLE_MAX_UPLOAD_SIZE'),
            'box_colors' => Configuration::get('ART_PUZZLE_BOX_COLORS'),
            'fonts' => Configuration::get('ART_PUZZLE_FONTS'),
        ]);

        $this->setTemplate('module:art_puzzle/views/templates/front/customizer.tpl');
    }

    private function logAndError($message)
    {
        $this->errors[] = $this->module->l($message);
        if (class_exists('ArtPuzzleLogger')) {
            ArtPuzzleLogger::log('[CUSTOMIZER] ' . $message);
        }
    }
}
