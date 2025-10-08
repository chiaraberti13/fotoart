<?php
/**
 * FotoArt Puzzle - Front Wizard Controller
 * Provides a dedicated entry point for the customization wizard
 */

class FotoartpuzzleIndexModuleFrontController extends ModuleFrontController
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * {@inheritdoc}
     */
    public $ssl = true;

    /**
     * Initialize controller
     */
    public function init()
    {
        parent::init();
        $this->initProduct();
    }

    /**
     * Load wizard assets
     */
    public function setMedia()
    {
        parent::setMedia();

        $this->registerJavascript(
            'modules-fotoartpuzzle-wizard',
            'modules/' . $this->module->name . '/views/js/puzzle.js',
            [
                'position' => 'bottom',
                'priority' => 150,
            ]
        );

        $this->registerStylesheet(
            'modules-fotoartpuzzle-wizard',
            'modules/' . $this->module->name . '/views/css/puzzle.css',
            [
                'media' => 'all',
                'priority' => 150,
            ]
        );
    }

    /**
     * Render wizard page
     */
    public function initContent()
    {
        parent::initContent();

        if (!$this->product || !$this->product->id) {
            Tools::redirect('index.php?controller=404');
        }

        $config = FAPConfiguration::getFrontConfig();

        $this->context->smarty->assign([
            'product' => $this->product,
            'product_link' => $this->context->link->getProductLink($this->product),
            'wizard' => [
                'config_json' => Tools::jsonEncode($config),
                'upload_url' => $this->context->link->getModuleLink($this->module->name, 'upload'),
                'preview_url' => $this->context->link->getModuleLink($this->module->name, 'preview'),
                'summary_url' => $this->context->link->getModuleLink($this->module->name, 'summary'),
                'ajax_url' => $this->context->link->getModuleLink($this->module->name, 'ajax'),
                'token_upload' => $this->module->getFrontToken('upload'),
                'token_preview' => $this->module->getFrontToken('preview'),
                'token_summary' => $this->module->getFrontToken('summary'),
                'token_ajax' => $this->module->getFrontToken('ajax'),
            ],
        ]);

        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/wizard.tpl');
    }

    /**
     * Initialize product from request
     */
    protected function initProduct()
    {
        $idProduct = (int) Tools::getValue('id_product');

        if (!$idProduct) {
            Tools::redirect('index.php?controller=404');
        }

        $product = new Product($idProduct, true, $this->context->language->id);

        if (!$product || !$product->id || !$product->active || !FAPConfiguration::isProductEnabled($idProduct)) {
            Tools::redirect('index.php?controller=404');
        }

        $this->product = $product;
        $this->context->smarty->assign('product', $this->product);
        $this->context->smarty->assign('meta_title', $this->product->name);
    }
}
