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
        $structuredMetadata = self::buildMetadataPayload(
            (int) $idProduct,
            (int) $idProductAttribute,
            $imagePath,
            $boxText,
            $metadata
        );
        self::saveCustomizationData(
            $customization->id,
            Product::CUSTOMIZE_TEXTFIELD,
            1,
            self::encodeMetadata($structuredMetadata)
        );

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
            $metadata = self::ensureMetadataStructure(
                is_array($customization['metadata']) ? $customization['metadata'] : []
            );

            if (!empty($customization['file']) && file_exists($customization['file'])) {
                $destination = self::copyAssetToOrder(
                    $customization['file'],
                    $orderPath,
                    'asset',
                    $customization['id_customization']
                );
                self::saveCustomizationData(
                    $customization['id_customization'],
                    Product::CUSTOMIZE_FILE,
                    0,
                    $destination
                );
                $metadata = self::updateMetadataAssetPath($metadata, ['asset_map', 'original', 'path'], $destination, 'image_path');
            }

            $metadataUpdated = false;
            $assetDescriptors = self::collectAssetDescriptors($metadata);

            foreach ($assetDescriptors as $descriptor) {
                if (empty($descriptor['path']) || !file_exists($descriptor['path'])) {
                    continue;
                }

                $destination = self::copyAssetToOrder(
                    $descriptor['path'],
                    $orderPath,
                    $descriptor['prefix'],
                    $customization['id_customization']
                );

                $metadata = self::updateMetadataAssetPath(
                    $metadata,
                    $descriptor['key'],
                    $destination,
                    isset($descriptor['alias']) ? $descriptor['alias'] : null
                );
                $metadataUpdated = true;
            }

            if ($metadataUpdated) {
                self::saveCustomizationData(
                    $customization['id_customization'],
                    Product::CUSTOMIZE_TEXTFIELD,
                    1,
                    self::encodeMetadata($metadata)
                );
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

            $metadata = self::ensureMetadataStructure(
                is_array($customization['metadata']) ? $customization['metadata'] : []
            );

            $assetDescriptors = self::collectAssetDescriptors($metadata);
            foreach ($assetDescriptors as $descriptor) {
                if (empty($descriptor['path']) || !file_exists($descriptor['path'])) {
                    continue;
                }

                $assets[] = [
                    'type' => $descriptor['type'],
                    'id_customization' => $customization['id_customization'],
                    'path' => $descriptor['path'],
                    'filename' => 'customization_' . (int) $customization['id_customization'] . '_' . $descriptor['type']
                        . '_' . basename($descriptor['path']),
                ];
            }
        }

        return $assets;
    }

    /**
     * Persist updated metadata for a customization entry.
     *
     * @param int $idCustomization
     * @param array $metadata
     */
    public static function saveMetadata($idCustomization, array $metadata)
    {
        $normalized = self::ensureMetadataStructure($metadata);

        self::saveCustomizationData(
            (int) $idCustomization,
            Product::CUSTOMIZE_TEXTFIELD,
            1,
            self::encodeMetadata($normalized)
        );
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
                    $customizations[$id]['metadata'] = self::decodeMetadata($row['value']);
                }
            }
        }

        return array_values($customizations);
    }

    /**
     * Prepare a normalized metadata payload including legacy aliases.
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param string $imagePath
     * @param string $boxText
     * @param array $metadata
     *
     * @return array
     */
    private static function buildMetadataPayload($idProduct, $idProductAttribute, $imagePath, $boxText, array $metadata)
    {
        $formatDetails = self::ensureArray(isset($metadata['format_details']) ? $metadata['format_details'] : []);
        $formatPayload = self::ensureArray(isset($metadata['format_payload']) ? $metadata['format_payload'] : []);
        if (!$formatDetails && $formatPayload) {
            $formatDetails = $formatPayload;
        }

        $boxPayload = self::ensureArray(isset($metadata['box_payload']) ? $metadata['box_payload'] : []);

        $coordinates = self::normaliseCoordinates($metadata, $formatDetails);
        $crop = self::normaliseCropData($metadata, $coordinates);

        $orientation = self::extractString($metadata, 'orientation');
        if (!$orientation && isset($formatDetails['orientation'])) {
            $orientation = (string) $formatDetails['orientation'];
        }

        $qualityScore = self::extractInt($metadata, 'quality', isset($formatDetails['quality']) ? $formatDetails['quality'] : null);
        $pieces = self::extractInt($metadata, 'pieces', isset($formatDetails['pieces']) ? $formatDetails['pieces'] : null);

        $previewPath = self::extractString($metadata, 'preview_path');
        $previewUrl = self::extractString($metadata, 'preview_url');
        $thumbnailPath = self::extractString($metadata, 'thumbnail_path');
        $thumbnailUrl = self::extractString($metadata, 'thumbnail_url');
        $downloadUrl = self::extractString($metadata, 'download_url');

        $printable = self::extractBool($metadata, 'printable');

        $formatName = self::extractString($metadata, 'format');
        if (!$formatName && isset($formatDetails['name'])) {
            $formatName = (string) $formatDetails['name'];
        }

        $formatInfo = self::filterEmpty([
            'id' => self::extractInt($metadata, 'format_id', isset($formatDetails['id']) ? $formatDetails['id'] : null),
            'reference' => self::extractString($metadata, 'format_reference', isset($formatDetails['reference']) ? $formatDetails['reference'] : null),
            'name' => $formatName,
            'pieces' => $pieces,
            'quality' => $qualityScore,
            'coordinates' => $coordinates,
            'details' => $formatDetails,
        ]);

        $boxInfo = self::filterEmpty(array_merge($boxPayload, [
            'id' => self::extractInt($metadata, 'box_id'),
            'reference' => self::extractString($metadata, 'box_reference'),
            'name' => self::extractString($metadata, 'box_name'),
            'text' => $boxText,
            'color' => self::extractString($metadata, 'color'),
            'font' => self::extractString($metadata, 'font'),
        ]));

        $imageInfo = self::filterEmpty([
            'path' => $imagePath,
            'width' => self::extractInt($metadata, 'image_width'),
            'height' => self::extractInt($metadata, 'image_height'),
            'orientation' => $orientation,
        ]);

        if ((!isset($imageInfo['width']) || !isset($imageInfo['height'])) && file_exists($imagePath)) {
            $size = @getimagesize($imagePath);
            if (is_array($size)) {
                $imageInfo['width'] = isset($imageInfo['width']) ? $imageInfo['width'] : (int) $size[0];
                $imageInfo['height'] = isset($imageInfo['height']) ? $imageInfo['height'] : (int) $size[1];
            }
        }

        if (!$orientation && isset($imageInfo['width']) && isset($imageInfo['height'])) {
            $orientation = (int) $imageInfo['width'] >= (int) $imageInfo['height'] ? 'landscape' : 'portrait';
            $imageInfo['orientation'] = $orientation;
        }

        $assetMap = self::filterEmpty([
            'original' => self::filterEmpty([
                'path' => $imagePath,
                'url' => $downloadUrl,
            ]),
            'preview' => self::filterEmpty([
                'path' => $previewPath,
                'url' => $previewUrl,
            ]),
            'thumbnail' => self::filterEmpty([
                'path' => $thumbnailPath,
                'url' => $thumbnailUrl,
            ]),
        ]);

        $printInfo = self::filterEmpty([
            'quality' => $qualityScore,
            'quality_label' => self::qualityLabel($qualityScore),
            'printable' => $printable,
        ]);

        $notes = self::filterEmpty([
            'pdf' => self::extractString($metadata, 'pdf_note'),
            'download' => self::extractString($metadata, 'download_note'),
        ]);

        $payload = self::filterEmpty([
            'version' => 2,
            'product' => [
                'id' => (int) $idProduct,
                'attribute_id' => (int) $idProductAttribute,
            ],
            'format' => $formatName,
            'color' => isset($boxInfo['color']) ? $boxInfo['color'] : null,
            'font' => isset($boxInfo['font']) ? $boxInfo['font'] : null,
            'format_id' => isset($formatInfo['id']) ? $formatInfo['id'] : null,
            'format_reference' => isset($formatInfo['reference']) ? $formatInfo['reference'] : null,
            'box_id' => isset($boxInfo['id']) ? $boxInfo['id'] : null,
            'box_reference' => isset($boxInfo['reference']) ? $boxInfo['reference'] : null,
            'box_name' => isset($boxInfo['name']) ? $boxInfo['name'] : null,
            'preview_path' => $previewPath,
            'preview_url' => $previewUrl,
            'thumbnail_path' => $thumbnailPath,
            'thumbnail_url' => $thumbnailUrl,
            'download_url' => $downloadUrl,
            'orientation' => $orientation,
            'image_path' => $imagePath,
            'image_width' => isset($imageInfo['width']) ? $imageInfo['width'] : null,
            'image_height' => isset($imageInfo['height']) ? $imageInfo['height'] : null,
            'pieces' => $pieces,
            'quality' => $qualityScore,
            'printable' => $printable,
            'format_details' => $formatDetails,
            'box_details' => $boxPayload,
            'image_info' => $imageInfo,
            'format_info' => $formatInfo,
            'box_info' => $boxInfo,
            'print_info' => $printInfo,
            'asset_map' => $assetMap,
            'coordinates' => $coordinates,
            'crop' => $crop,
            'notes' => $notes,
            'timestamps' => [
                'generated_at' => date(DATE_ATOM),
            ],
        ]);

        return $payload;
    }

    /**
     * Convert raw metadata JSON into an associative array and upgrade legacy payloads.
     *
     * @param string $value
     *
     * @return array
     */
    private static function decodeMetadata($value)
    {
        if (!is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return [];
        }

        if (!isset($decoded['version'])) {
            $decoded = self::upgradeLegacyMetadata($decoded);
        }

        return $decoded;
    }

    /**
     * Upgrade metadata stored by earlier versions of the module.
     *
     * @param array $metadata
     *
     * @return array
     */
    private static function upgradeLegacyMetadata(array $metadata)
    {
        $formatDetails = self::ensureArray(isset($metadata['format_details']) ? $metadata['format_details'] : []);
        $boxPayload = self::ensureArray(isset($metadata['box_details']) ? $metadata['box_details'] : []);

        $metadata['version'] = 1;

        if (!isset($metadata['format_info'])) {
            $metadata['format_info'] = self::filterEmpty([
                'id' => isset($metadata['format_id']) ? (int) $metadata['format_id'] : null,
                'reference' => isset($metadata['format_reference']) ? $metadata['format_reference'] : null,
                'name' => isset($metadata['format']) ? $metadata['format'] : (isset($formatDetails['name']) ? $formatDetails['name'] : null),
                'details' => $formatDetails,
            ]);
        }

        if (!isset($metadata['box_info'])) {
            $metadata['box_info'] = self::filterEmpty(array_merge($boxPayload, [
                'id' => isset($metadata['box_id']) ? (int) $metadata['box_id'] : null,
                'reference' => isset($metadata['box_reference']) ? $metadata['box_reference'] : null,
                'name' => isset($metadata['box_name']) ? $metadata['box_name'] : null,
                'color' => isset($metadata['color']) ? $metadata['color'] : null,
                'font' => isset($metadata['font']) ? $metadata['font'] : null,
            ]));
        }

        if (!isset($metadata['asset_map'])) {
            $metadata['asset_map'] = [];
        }

        if (!empty($metadata['preview_path']) && empty($metadata['asset_map']['preview'])) {
            $metadata['asset_map']['preview'] = ['path' => $metadata['preview_path']];
        }

        if (!empty($metadata['thumbnail_path']) && empty($metadata['asset_map']['thumbnail'])) {
            $metadata['asset_map']['thumbnail'] = ['path' => $metadata['thumbnail_path']];
        }

        if (!empty($metadata['download_url']) && empty($metadata['asset_map']['original'])) {
            $metadata['asset_map']['original'] = [
                'path' => isset($metadata['image_path']) ? $metadata['image_path'] : null,
                'url' => $metadata['download_url'],
            ];
        }

        if (!isset($metadata['image_info'])) {
            $metadata['image_info'] = self::filterEmpty([
                'width' => isset($metadata['image_width']) ? (int) $metadata['image_width'] : null,
                'height' => isset($metadata['image_height']) ? (int) $metadata['image_height'] : null,
                'orientation' => isset($metadata['orientation']) ? $metadata['orientation'] : null,
            ]);
        }

        if (!isset($metadata['print_info']) && isset($metadata['quality'])) {
            $metadata['print_info'] = self::filterEmpty([
                'quality' => (int) $metadata['quality'],
                'quality_label' => self::qualityLabel(isset($metadata['quality']) ? (int) $metadata['quality'] : null),
                'printable' => isset($metadata['printable']) ? (bool) $metadata['printable'] : null,
            ]);
        }

        if (!isset($metadata['timestamps'])) {
            $metadata['timestamps'] = [
                'generated_at' => date(DATE_ATOM),
            ];
        }

        return $metadata;
    }

    /**
     * Ensure metadata array contains baseline structure.
     *
     * @param array $metadata
     *
     * @return array
     */
    private static function ensureMetadataStructure(array $metadata)
    {
        if (!isset($metadata['version'])) {
            $metadata = self::upgradeLegacyMetadata($metadata);
        }

        if (!isset($metadata['asset_map']) || !is_array($metadata['asset_map'])) {
            $metadata['asset_map'] = [];
        }

        return $metadata;
    }

    /**
     * Collect descriptors for preview/thumbnail assets.
     *
     * @param array $metadata
     *
     * @return array
     */
    private static function collectAssetDescriptors(array $metadata)
    {
        $descriptors = [];
        $seen = [];

        if (!empty($metadata['asset_map']) && is_array($metadata['asset_map'])) {
            foreach ($metadata['asset_map'] as $key => $asset) {
                if ($key === 'original') {
                    continue;
                }

                if (!is_array($asset) || empty($asset['path'])) {
                    continue;
                }

                $path = (string) $asset['path'];
                if (isset($seen[$path])) {
                    continue;
                }
                $seen[$path] = true;

                $descriptors[] = [
                    'type' => $key,
                    'path' => $path,
                    'prefix' => $key,
                    'key' => ['asset_map', $key, 'path'],
                    'alias' => $key . '_path',
                ];
            }
        }

        foreach (['preview', 'thumbnail'] as $legacyKey) {
            $legacyPathKey = $legacyKey . '_path';
            if (!empty($metadata[$legacyPathKey])) {
                $path = (string) $metadata[$legacyPathKey];
                if (!isset($seen[$path])) {
                    $seen[$path] = true;
                    $descriptors[] = [
                        'type' => $legacyKey,
                        'path' => $path,
                        'prefix' => $legacyKey,
                        'key' => ['asset_map', $legacyKey, 'path'],
                        'alias' => $legacyPathKey,
                    ];
                }
            }
        }

        return $descriptors;
    }

    /**
     * Update metadata with a new asset path and propagate aliases.
     *
     * @param array $metadata
     * @param array $keyPath
     * @param string $destination
     * @param string|null $alias
     *
     * @return array
     */
    private static function updateMetadataAssetPath(array $metadata, array $keyPath, $destination, $alias)
    {
        if ($destination === null || $destination === '') {
            return $metadata;
        }

        $ref =& $metadata;
        $lastIndex = count($keyPath) - 1;
        foreach ($keyPath as $index => $segment) {
            if (!is_array($ref)) {
                $ref = [];
            }

            if ($index === $lastIndex) {
                $ref[$segment] = $destination;
            } else {
                if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                    $ref[$segment] = [];
                }
                $ref =& $ref[$segment];
            }
        }

        if ($alias !== null) {
            $metadata[$alias] = $destination;
        }

        return $metadata;
    }

    /**
     * Encode metadata into JSON.
     *
     * @param array $metadata
     *
     * @return string
     */
    private static function encodeMetadata(array $metadata)
    {
        return json_encode($metadata, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Ensure a metadata value is treated as array.
     *
     * @param mixed $value
     *
     * @return array
     */
    private static function ensureArray($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Normalise coordinates from metadata payloads.
     *
     * @param array $metadata
     * @param array $formatDetails
     *
     * @return array
     */
    private static function normaliseCoordinates(array $metadata, array $formatDetails)
    {
        if (isset($metadata['coordinates'])) {
            $coordinates = self::ensureArray($metadata['coordinates']);
            if ($coordinates) {
                return $coordinates;
            }
        }

        if (isset($metadata['crop']['coordinates'])) {
            $coordinates = self::ensureArray($metadata['crop']['coordinates']);
            if ($coordinates) {
                return $coordinates;
            }
        }

        if (isset($formatDetails['coordinates'])) {
            $coordinates = self::ensureArray($formatDetails['coordinates']);
            if ($coordinates) {
                return $coordinates;
            }
        }

        return [];
    }

    /**
     * Normalise crop data using provided metadata.
     *
     * @param array $metadata
     * @param array $coordinates
     *
     * @return array
     */
    private static function normaliseCropData(array $metadata, array $coordinates)
    {
        $crop = [];

        if (isset($metadata['crop'])) {
            $crop = self::ensureArray($metadata['crop']);
        }

        if (!isset($crop['coordinates']) && $coordinates) {
            $crop['coordinates'] = $coordinates;
        }

        if (isset($metadata['orientation']) && !isset($crop['orientation'])) {
            $crop['orientation'] = $metadata['orientation'];
        }

        return self::filterEmpty($crop);
    }

    /**
     * Extract a string from metadata array.
     *
     * @param array $metadata
     * @param string $key
     * @param mixed $default
     *
     * @return string|null
     */
    private static function extractString(array $metadata, $key, $default = null)
    {
        if (isset($metadata[$key])) {
            $value = $metadata[$key];
            if ($value === '') {
                return '';
            }

            return (string) $value;
        }

        return $default !== null ? (string) $default : null;
    }

    /**
     * Extract an integer value from metadata.
     *
     * @param array $metadata
     * @param string $key
     * @param mixed $default
     *
     * @return int|null
     */
    private static function extractInt(array $metadata, $key, $default = null)
    {
        if (isset($metadata[$key]) && $metadata[$key] !== '') {
            return (int) $metadata[$key];
        }

        if ($default !== null && $default !== '') {
            return (int) $default;
        }

        return null;
    }

    /**
     * Extract a boolean metadata value.
     *
     * @param array $metadata
     * @param string $key
     *
     * @return bool|null
     */
    private static function extractBool(array $metadata, $key)
    {
        if (!isset($metadata[$key])) {
            return null;
        }

        return (bool) $metadata[$key];
    }

    /**
     * Recursively filter null/empty values while preserving numeric zero.
     *
     * @param array $values
     *
     * @return array
     */
    private static function filterEmpty(array $values)
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = self::filterEmpty($value);
                if ($values[$key] === []) {
                    unset($values[$key]);
                }
                continue;
            }

            if ($value === null || $value === '') {
                unset($values[$key]);
            }
        }

        return $values;
    }

    /**
     * Translate a quality score into a label.
     *
     * @param int|null $score
     *
     * @return string|null
     */
    private static function qualityLabel($score)
    {
        if ($score === null) {
            return null;
        }

        switch ((int) $score) {
            case 4:
                return 'excellent';
            case 3:
                return 'great';
            case 2:
                return 'good';
            case 1:
                return 'poor';
            default:
                return 'insufficient';
        }
    }
}
