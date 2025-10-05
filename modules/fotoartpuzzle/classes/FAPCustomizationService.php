<?php

class FAPCustomizationService
{
    /**
     * Create customization entry for cart
     *
     * @param Cart $cart
     * @param int $idProduct
     * @param string $imagePath
     * @param string $boxText
     * @param array $metadata
     *
     * @return int
     */
    public static function createCustomization(Cart $cart, $idProduct, $imagePath, $boxText, array $metadata, $idProductAttribute = 0)
    {
        $context = Context::getContext();
        $customization = self::getOrCreateCustomization($cart->id, $idProduct, (int) $idProductAttribute, $context->shop->id);

        self::saveCustomizationData($customization->id, Product::CUSTOMIZE_FILE, 0, $imagePath);
        self::saveCustomizationData($customization->id, Product::CUSTOMIZE_TEXTFIELD, 0, $boxText);
        self::saveCustomizationData($customization->id, Product::CUSTOMIZE_TEXTFIELD, 1, json_encode($metadata));

        Db::getInstance()->update('cart_product', [
            'id_customization' => (int) $customization->id,
        ], 'id_cart = ' . (int) $cart->id . ' AND id_product = ' . (int) $idProduct . ' AND id_product_attribute = ' . (int) $idProductAttribute);

        return (int) $customization->id;
    }

    /**
     * Get customizations for cart
     *
     * @param int $idCart
     *
     * @return array
     */
    public static function getCartCustomizations($idCart)
    {
        $sql = 'SELECT c.`id_customization`, cd.`type`, cd.`value`, cd.`index`
                FROM `' . _DB_PREFIX_ . "customization` c
                INNER JOIN `' . _DB_PREFIX_ . "customized_data` cd ON cd.`id_customization` = c.`id_customization`
                WHERE c.`id_cart` = ' . (int) $idCart;

        $rows = Db::getInstance()->executeS($sql) ?: [];

        return self::hydrateCustomizations($rows);
    }

    /**
     * Get customizations for order
     *
     * @param int $idOrder
     *
     * @return array
     */
    public static function getOrderCustomizations($idOrder)
    {
        $sql = 'SELECT c.`id_customization`, cd.`type`, cd.`value`, cd.`index`
                FROM `' . _DB_PREFIX_ . "customized_data` cd
                INNER JOIN `' . _DB_PREFIX_ . "customization` c ON c.`id_customization` = cd.`id_customization`
                INNER JOIN `' . _DB_PREFIX_ . "order_detail` od ON od.`id_customization` = c.`id_customization`
                WHERE od.`id_order` = ' . (int) $idOrder;

        $rows = Db::getInstance()->executeS($sql) ?: [];

        return self::hydrateCustomizations($rows);
    }

    /**
     * Finalize customizations when order is created
     *
     * @param Order $order
     */
    public static function finalizeOrderCustomizations(Order $order)
    {
        $idOrder = (int) $order->id;
        $orderPath = FAPPathBuilder::getOrderPath($idOrder);
        if (!is_dir($orderPath)) {
            @mkdir($orderPath, 0750, true);
        }

        $query = new DbQuery();
        $query->select('cd.value');
        $query->from('customized_data', 'cd');
        $query->innerJoin('customization', 'c', 'c.id_customization = cd.id_customization');
        $query->innerJoin('cart_product', 'cp', 'cp.id_customization = c.id_customization');
        $query->where('c.id_cart = ' . (int) $order->id_cart);
        $query->where('cd.type = ' . (int) Product::CUSTOMIZE_FILE);

        $files = Db::getInstance()->executeS($query);
        if (!$files) {
            return;
        }

        foreach ($files as $file) {
            $source = $file['value'];
            if (!file_exists($source)) {
                continue;
            }
            $destination = $orderPath . '/' . basename($source);
            Tools::copy($source, $destination);
        }
    }

    /**
     * Retrieve or create customization object
     *
     * @param int $idCart
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idShop
     *
     * @return Customization
     */
    private static function getOrCreateCustomization($idCart, $idProduct, $idProductAttribute, $idShop)
    {
        $idCustomization = (int) Db::getInstance()->getValue(
            'SELECT `id_customization`
             FROM `' . _DB_PREFIX_ . 'customization`
             WHERE `id_cart` = ' . (int) $idCart . '
               AND `id_product` = ' . (int) $idProduct . '
               AND `id_product_attribute` = ' . (int) $idProductAttribute
             ORDER BY `id_customization` DESC'
        );

        if ($idCustomization) {
            return new Customization($idCustomization);
        }

        $customization = new Customization();
        $customization->id_cart = (int) $idCart;
        $customization->id_product = (int) $idProduct;
        $customization->id_product_attribute = (int) $idProductAttribute;
        $customization->id_shop = (int) $idShop;
        $customization->quantity = 1;
        $customization->in_cart = 1;
        $customization->add();

        return $customization;
    }

    /**
     * Store a row in customized_data table
     *
     * @param int $idCustomization
     * @param int $type
     * @param int $index
     * @param string $value
     */
    private static function saveCustomizationData($idCustomization, $type, $index, $value)
    {
        Db::getInstance()->delete('customized_data', 'id_customization = ' . (int) $idCustomization . ' AND type = ' . (int) $type . ' AND `index` = ' . (int) $index);

        Db::getInstance()->insert('customized_data', [
            'id_customization' => (int) $idCustomization,
            'type' => (int) $type,
            'index' => (int) $index,
            'value' => pSQL($value, true),
        ]);
    }

    /**
     * Normalize database rows into structured customizations
     *
     * @param array $rows
     *
     * @return array
     */
    private static function hydrateCustomizations(array $rows)
    {
        $customizations = [];
        foreach ($rows as $row) {
            $id = (int) $row['id_customization'];
            if (!isset($customizations[$id])) {
                $customizations[$id] = [
                    'id_customization' => $id,
                    'file' => null,
                    'text' => null,
                    'metadata' => [],
                ];
            }

            if ((int) $row['type'] === Product::CUSTOMIZE_FILE) {
                $customizations[$id]['file'] = $row['value'];
            }

            if ((int) $row['type'] === Product::CUSTOMIZE_TEXTFIELD) {
                if ((int) $row['index'] === 0) {
                    $customizations[$id]['text'] = $row['value'];
                }

                if ((int) $row['index'] === 1) {
                    $decoded = json_decode($row['value'], true);
                    if (is_array($decoded)) {
                        $customizations[$id]['metadata'] = $decoded;
                    }
                }
            }
        }

        return array_values($customizations);
    }
}
