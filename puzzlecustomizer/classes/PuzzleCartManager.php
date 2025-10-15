<?php

class PuzzleCartManager
{
    /**
     * Add customized product to cart.
     *
     * @param int $idProduct
     * @param int $idCustomization
     * @param int $quantity
     * @param Context|null $context
     *
     * @return bool
     */
    public static function addToCart($idProduct, $idCustomization, $quantity, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if (!$context->cart || !$context->cart->id) {
            $context->cart = new Cart();
            $context->cart->id_customer = (int) $context->customer->id;
            $context->cart->id_lang = (int) $context->language->id;
            $context->cart->id_currency = (int) $context->currency->id;
            $context->cart->id_address_delivery = (int) Address::getFirstCustomerAddressId($context->customer->id);
            $context->cart->id_address_invoice = $context->cart->id_address_delivery;

            if (!$context->cart->add()) {
                return false;
            }

            $context->cookie->id_cart = (int) $context->cart->id;
        }

        $customization = new PuzzleCustomization($idCustomization);
        if (!Validate::isLoadedObject($customization)) {
            return false;
        }

        $idPsCustomization = self::createPrestaShopCustomization(
            $context->cart->id,
            $idProduct,
            $customization
        );

        if (!$idPsCustomization) {
            return false;
        }

        $result = $context->cart->updateQty(
            $quantity,
            $idProduct,
            null,
            $idPsCustomization,
            'up'
        );

        if ($result) {
            $customization->id_cart = (int) $context->cart->id;
            $customization->status = 'in_cart';
            $customization->save();
        }

        return (bool) $result;
    }

    /**
     * Create PrestaShop customization entry.
     */
    protected static function createPrestaShopCustomization($idCart, $idProduct, PuzzleCustomization $puzzleCustomization)
    {
        $config = json_decode($puzzleCustomization->configuration, true);

        $customizationFields = Product::getCustomizationFieldIds($idProduct);

        if (empty($customizationFields)) {
            self::createCustomizationFields($idProduct);
            $customizationFields = Product::getCustomizationFieldIds($idProduct);
        }

        $idCustomization = (int) Db::getInstance()->getValue(
            'SELECT MAX(id_customization) FROM ' . _DB_PREFIX_ . 'customization'
            . ' WHERE id_cart = ' . (int) $idCart . ' AND id_product = ' . (int) $idProduct
        ) + 1;

        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'customization'
            . ' (id_customization, id_cart, id_product, id_product_attribute, id_address_delivery, quantity, in_cart)'
            . ' VALUES ('
            . (int) $idCustomization . ', '
            . (int) $idCart . ', '
            . (int) $idProduct . ', '
            . '0, 0, 0, 1)';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        foreach ($customizationFields as $field) {
            $idCustomizationField = (int) $field['id_customization_field'];
            $type = (int) $field['type'];

            if ($type === Product::CUSTOMIZE_FILE) {
                $value = $puzzleCustomization->image_path;
            } elseif ($type === Product::CUSTOMIZE_TEXTFIELD) {
                $value = isset($config['text_content']) ? $config['text_content'] : '';
            } else {
                $value = '';
            }

            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'customized_data'
                . ' (id_customization, type, index, value) VALUES ('
                . (int) $idCustomization . ', '
                . (int) $type . ', '
                . (int) $idCustomizationField . ', "' . pSQL($value) . '")';

            Db::getInstance()->execute($sql);
        }

        return $idCustomization;
    }

    /**
     * Create customization fields for product.
     */
    protected static function createCustomizationFields($idProduct)
    {
        Db::getInstance()->insert('customization_field', [
            'id_product' => (int) $idProduct,
            'type' => Product::CUSTOMIZE_FILE,
            'required' => 1,
        ]);

        $idImageField = Db::getInstance()->Insert_ID();

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            Db::getInstance()->insert('customization_field_lang', [
                'id_customization_field' => (int) $idImageField,
                'id_lang' => (int) $lang['id_lang'],
                'name' => 'Puzzle Image',
            ]);
        }

        Db::getInstance()->insert('customization_field', [
            'id_product' => (int) $idProduct,
            'type' => Product::CUSTOMIZE_TEXTFIELD,
            'required' => 0,
        ]);

        $idTextField = Db::getInstance()->Insert_ID();

        foreach ($languages as $lang) {
            Db::getInstance()->insert('customization_field_lang', [
                'id_customization_field' => (int) $idTextField,
                'id_lang' => (int) $lang['id_lang'],
                'name' => 'Custom Text',
            ]);
        }

        Db::getInstance()->update('product', [
            'customizable' => 2,
            'uploadable_files' => 1,
            'text_fields' => 1,
        ], 'id_product = ' . (int) $idProduct);

        Cache::clean('Product::getCustomizationFieldIds_' . (int) $idProduct . '_*');
    }
}
