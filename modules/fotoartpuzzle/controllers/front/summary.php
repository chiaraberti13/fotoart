<?php

class FotoartpuzzleSummaryModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
    }

    public function initContent()
    {
        parent::initContent();
        $this->ajaxDie(json_encode($this->handleRequest()));
    }

    private function handleRequest()
    {
        try {
            $this->validateToken();
            $cart = $this->context->cart;
            if (!$cart || !$cart->id) {
                throw new Exception($this->module->l('Cart not available.'));
            }

            $idProduct = (int) Tools::getValue('id_product');
            $idProductAttribute = (int) Tools::getValue('id_product_attribute');
            $imagePath = Tools::getValue('file');
            $boxText = (string) Tools::getValue('box_text', '');
            $boxColor = (string) Tools::getValue('box_color', '#FFFFFF');
            $boxFont = (string) Tools::getValue('box_font', 'Roboto-Regular');
            $format = Tools::getValue('format');

            if (!$idProduct || !$imagePath || !file_exists($imagePath)) {
                throw new Exception($this->module->l('Missing data to create customization.'));
            }

            $metadata = [
                'color' => $boxColor,
                'font' => $boxFont,
                'format' => $format,
            ];

            $idCustomization = FAPCustomizationService::createCustomization($cart, $idProduct, $imagePath, $boxText, $metadata, $idProductAttribute);

            if (!$cart->updateQty(1, $idProduct, $idProductAttribute, $idCustomization, 'up')) {
                throw new Exception($this->module->l('Unable to add the customized puzzle to the cart.'));
            }

            return [
                'success' => true,
                'id_customization' => $idCustomization,
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    private function validateToken()
    {
        $token = Tools::getValue('token');
        if (!$token || $token !== $this->module->getFrontToken('summary')) {
            throw new Exception($this->module->l('Invalid token.'));
        }
    }
}
