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
        $sql = 'SELECT c.`id_customization`, cd.`type`, cd.`value`, cd.`index`'
            . ' FROM `' . _DB_PREFIX_ . 'customization` c'
            . ' INNER JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON cd.`id_customization` = c.`id_customization`'
            . ' WHERE c.`id_cart` = ' . (int) $idCart;

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
        $sql = 'SELECT c.`id_customization`, cd.`type`, cd.`value`, cd.`index`'
            . ' FROM `' . _DB_PREFIX_ . 'customized_data` cd'
            . ' INNER JOIN `' . _DB_PREFIX_ . 'customization` c ON c.`id_customization` = cd.`id_customization`'
            . ' INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od ON od.`id_customization` = c.`id_customization`'
            . ' WHERE od.`id_order` = ' . (int) $idOrder;

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

        $customizations = self::getCartCustomizations($order->id_cart);
        if (!$customizations) {
            return;
        }

        foreach ($customizations as $customization) {
            $metadata = is_array($customization['metadata']) ? $customization['metadata'] : [];

            if (!empty($customization['file']) && file_exists($customization['file'])) {
                $destination = self::copyAssetToOrder($customization['file'], $orderPath, 'asset', $customization['id_customization']);
                self::saveCustomizationData($customization['id_customization'], Product::CUSTOMIZE_FILE, 0, $destination);
            }

            if (!empty($metadata['preview_path']) && file_exists($metadata['preview_path'])) {
                $previewDestination = self::copyAssetToOrder($metadata['preview_path'], $orderPath, 'preview', $customization['id_customization']);
                $metadata['preview_path'] = $previewDestination;
                self::saveCustomizationData($customization['id_customization'], Product::CUSTOMIZE_TEXTFIELD, 1, json_encode($metadata));
            }
        }
    }

    /**
     * Collect paths to assets associated with an order
     *
     * @param int $idOrder
     *
     * @return array
     */
    public static function getOrderAssets($idOrder)
    {
        $assets = [];
        $customizations = self::getOrderCustomizations((int) $idOrder);

        foreach ($customizations as $customization) {
            if (!empty($customization['file']) && file_exists($customization['file'])) {
                $assets[] = [
                    'type' => 'image',
                    'id_customization' => $customization['id_customization'],
                    'path' => $customization['file'],
                    'filename' => 'customization_' . (int) $customization['id_customization'] . '_image_' . basename($customization['file']),
                ];
            }

            if (!empty($customization['metadata']['preview_path']) && file_exists($customization['metadata']['preview_path'])) {
                $assets[] = [
                    'type' => 'preview',
                    'id_customization' => $customization['id_customization'],
                    'path' => $customization['metadata']['preview_path'],
                    'filename' => 'customization_' . (int) $customization['id_customization'] . '_preview_' . basename($customization['metadata']['preview_path']),
                ];
            }
        }

        return $assets;
    }

    /**
     * Copy an asset into the order directory
     *
     * @param string $source
     * @param string $orderPath
     * @param string $prefix
     * @param int $idCustomization
     *
     * @return string
     */
    private static function copyAssetToOrder($source, $orderPath, $prefix, $idCustomization)
    {
        $extension = pathinfo($source, PATHINFO_EXTENSION);
        $filename = $prefix . '_' . (int) $idCustomization;
        if ($extension) {
            $filename .= '.' . $extension;
        }

        $destination = rtrim($orderPath, '/\\') . '/' . $filename;
        Tools::copy($source, $destination);

        return $destination;
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
            'SELECT `id_customization`'
            . ' FROM `' . _DB_PREFIX_ . 'customization`'
            . ' WHERE `id_cart` = ' . (int) $idCart
            . ' AND `id_product` = ' . (int) $idProduct
            . ' AND `id_product_attribute` = ' . (int) $idProductAttribute
            . ' ORDER BY `id_customization` DESC'
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
