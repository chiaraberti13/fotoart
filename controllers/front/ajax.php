<?php
/**
 * Art Puzzle AJAX Controller
 * VERSIONE CON INTEGRAZIONE CARRELLO
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Art_puzzleAjaxModuleFrontController extends ModuleFrontController
{
    public $ajax = true;
    
    public function init()
    {
        parent::init();
        $this->ajax = true;
    }
    
    public function initContent()
    {
        parent::initContent();
        $this->ajax = true;
    }
    
    public function displayAjax()
    {
        try {
            header('Content-Type: application/json');
            
            $action = Tools::getValue('action', 'test');
            
            switch ($action) {
                case 'uploadImage':
                    $this->handleUploadImage();
                    break;
                    
                case 'saveCustomization':
                    $this->handleSaveCustomization();
                    break;
                    
                case 'addToCart':
                    $this->handleAddToCart();
                    break;
                    
                case 'checkDirectoryPermissions':
                    $this->checkDirectoryPermissions();
                    break;
                    
                case 'test':
                default:
                    $response = [
                        'success' => true,
                        'message' => 'Controller AJAX funzionante',
                        'data' => [
                            'version' => '1.0.0',
                            'timestamp' => date('Y-m-d H:i:s')
                        ]
                    ];
                    die(json_encode($response));
                    break;
            }
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ];
            die(json_encode($response));
        }
    }
    
    /**
     * Gestisce upload immagine
     */
    protected function handleUploadImage()
    {
        $response = ['success' => false];
        
        if (!isset($_FILES['image'])) {
            $response['message'] = 'Nessuna immagine ricevuta';
            die(json_encode($response));
        }
        
        $file = $_FILES['image'];
        
        // Validazione
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed_types)) {
            $response['message'] = 'Tipo file non consentito';
            die(json_encode($response));
        }
        
        if ($file['size'] > 20 * 1024 * 1024) {
            $response['message'] = 'File troppo grande (max 20MB)';
            die(json_encode($response));
        }
        
        // Genera nome univoco
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('puzzle_') . '_' . time() . '.' . $extension;
        
        // Directory upload
        $upload_dir = _PS_MODULE_DIR_ . 'art_puzzle/upload/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $destination = $upload_dir . $filename;
        
        // Salva il file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Salva in sessione per riferimento futuro
            $this->context->cookie->art_puzzle_image = $filename;
            $this->context->cookie->write();
            
            $response['success'] = true;
            $response['message'] = 'Immagine caricata con successo';
            $response['data'] = [
                'filename' => $filename,
                'url' => $this->context->link->getBaseLink() . 'modules/art_puzzle/upload/' . $filename,
                'size' => $file['size']
            ];
        } else {
            $response['message'] = 'Errore nel salvataggio del file';
        }
        
        die(json_encode($response));
    }
    
    /**
     * Salva la personalizzazione completa
     */
    protected function handleSaveCustomization()
    {
        $response = ['success' => false];
        
        // Recupera i dati
        $this->module->ensureCustomizationStorageReady();

        $productId = (int)Tools::getValue('product_id');
        $formatId = Tools::getValue('format');
        if (!is_string($formatId)) {
            $formatId = '';
        }
        $formatId = trim($formatId);

        if ($productId <= 0) {
            $response['message'] = 'Prodotto non valido';
            die(json_encode($response));
        }

        $product = new Product($productId, false, $this->context->language->id);
        if (!Validate::isLoadedObject($product)) {
            $response['message'] = 'Prodotto non trovato';
            die(json_encode($response));
        }

        $formatData = $this->module->getProductFormatOption($productId, $formatId);
        if (!$formatData) {
            $response['message'] = 'Formato non valido';
            die(json_encode($response));
        }

        $canonicalFormatId = isset($formatData['id']) ? (string) $formatData['id'] : $formatId;

        $priceTaxIncl = (float)$formatData['price'];
        if ($priceTaxIncl <= 0) {
            $priceTaxIncl = (float)Product::getPriceStatic($productId, true, null, 6, null, false, true, 1, false, null, false, true);
        }

        $priceTaxExcl = $this->module->computePriceTaxExcl($priceTaxIncl, $product);

        $customization_data = [
            'product_id' => $productId,
            'format' => $canonicalFormatId,
            'format_name' => $formatData['name'],
            'price' => $priceTaxIncl,
            'price_tax_excl' => $priceTaxExcl,
            'box_text' => Tools::getValue('box_text'),
            'box_color' => Tools::getValue('box_color'),
            'box_font' => Tools::getValue('box_font'),
            'image_filename' => Tools::getValue('image_filename'),
            'customer_id' => (int)$this->context->customer->id,
            'date_add' => date('Y-m-d H:i:s')
        ];
        
        // Salva nel database (tabella custom se esiste)
        $table = _DB_PREFIX_ . 'art_puzzle_customization';
        
        // Verifica se la tabella esiste, altrimenti creala
        $sql_check = "SHOW TABLES LIKE '" . pSQL($table) . "'";
        if (!Db::getInstance()->executeS($sql_check)) {
            // Crea la tabella includendo i campi richiesti dalle logiche di prezzo
            $sql_create = "CREATE TABLE IF NOT EXISTS `" . pSQL($table) . "` (
                `id_customization` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `id_customer` int(11) DEFAULT NULL,
                `id_cart` int(11) DEFAULT NULL,
                `presta_customization_id` int(11) DEFAULT NULL,
                `id_order` int(11) DEFAULT NULL,
                `format` varchar(50) DEFAULT NULL,
                `price` decimal(10,2) DEFAULT NULL,
                `price_tax_excl` decimal(20,6) DEFAULT NULL,
                `box_text` varchar(255) DEFAULT NULL,
                `box_color` varchar(50) DEFAULT NULL,
                `box_font` varchar(100) DEFAULT NULL,
                `image_filename` varchar(255) DEFAULT NULL,
                `customization_data` text,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_customization`),
                KEY `id_product` (`id_product`),
                KEY `id_customer` (`id_customer`),
                KEY `id_cart` (`id_cart`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            Db::getInstance()->execute($sql_create);

            // Applica eventuali aggiornamenti schema/index richiesti dal modulo
            $this->module->ensureCustomizationStorageReady();
        }
        
        // Inserisci la personalizzazione
        $insert = Db::getInstance()->insert('art_puzzle_customization', [
            'id_product' => $customization_data['product_id'],
            'id_customer' => $customization_data['customer_id'],
            'id_cart' => (int)$this->context->cart->id,
            'format' => pSQL($customization_data['format']),
            'price' => $customization_data['price'],
            'price_tax_excl' => $customization_data['price_tax_excl'],
            'box_text' => pSQL($customization_data['box_text']),
            'box_color' => pSQL($customization_data['box_color']),
            'image_filename' => pSQL($customization_data['image_filename']),
            'customization_data' => json_encode($customization_data),
            'date_add' => $customization_data['date_add']
        ]);
        
        if ($insert) {
            $customization_id = Db::getInstance()->Insert_ID();
            
            // Salva l'ID in sessione per l'aggiunta al carrello
            $this->context->cookie->art_puzzle_customization_id = $customization_id;
            $this->context->cookie->write();
            
            $response['success'] = true;
            $response['message'] = 'Personalizzazione salvata';
            $response['data'] = [
                'customization_id' => $customization_id
            ];
        } else {
            $response['message'] = 'Errore nel salvataggio della personalizzazione';
        }
        
        die(json_encode($response));
    }
    
    /**
     * Aggiunge al carrello con personalizzazione
     */
    protected function handleAddToCart()
    {
        $response = ['success' => false];
        
        $product_id = (int)Tools::getValue('product_id');
        $customization_id = (int)Tools::getValue('customization_id');
        $format = Tools::getValue('format');
        if (!is_string($format)) {
            $format = '';
        }
        $format = trim($format);

        // Verifica che il prodotto esista
        $product = new Product($product_id, false, $this->context->language->id);
        if (!Validate::isLoadedObject($product)) {
            $response['message'] = 'Prodotto non trovato';
            die(json_encode($response));
        }

        $customRow = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'art_puzzle_customization` WHERE `id_customization` = ' . (int)$customization_id);
        if (!$customRow) {
            $response['message'] = 'Personalizzazione non trovata';
            die(json_encode($response));
        }

        // Crea o recupera il carrello
        if (!$this->context->cart->id) {
            $this->context->cart->add();
            $this->context->cookie->id_cart = (int)$this->context->cart->id;
            $this->context->cookie->write();
        }

        $prestaCustomization = new Customization();
        $prestaCustomization->id_product = $product_id;
        $prestaCustomization->id_product_attribute = 0;
        $prestaCustomization->id_cart = (int)$this->context->cart->id;
        $prestaCustomization->id_address_delivery = (int)$this->context->cart->id_address_delivery;
        $prestaCustomization->id_shop = (int)$this->context->shop->id;
        $prestaCustomization->id_shop_group = (int)$this->context->shop->id_shop_group;
        $prestaCustomization->quantity = 0;
        $prestaCustomization->in_cart = 0;
        $prestaCustomization->add();

        $prestaCustomizationId = (int)$prestaCustomization->id;

        // Aggiungi il prodotto al carrello
        $update_quantity = $this->context->cart->updateQty(
            1, // quantity
            $product_id,
            null, // id_product_attribute
            $prestaCustomizationId, // id_customization
            false, // operator
            'up', // operator for display
            0, // id_address_delivery
            null, // shop
            true // auto_add_cart_rule
        );
        
        if ($update_quantity) {
            $formatData = $this->module->getProductFormatOption($product_id, $format);
            if ($formatData) {
                $format = isset($formatData['id']) ? (string) $formatData['id'] : $format;
            } elseif (!empty($customRow['format'])) {
                $format = $customRow['format'];
            }

            // Aggiorna il prezzo se necessario (richiede override o modulo specifico)
            // Per ora salviamo il riferimento alla personalizzazione

            // Aggiorna la tabella di personalizzazione con l'ID del carrello
            if ($customization_id) {
                Db::getInstance()->update('art_puzzle_customization', [
                    'id_cart' => (int)$this->context->cart->id,
                    'presta_customization_id' => $prestaCustomizationId
                ], 'id_customization = ' . (int)$customization_id);

                $customRow['presta_customization_id'] = $prestaCustomizationId;

                if (!empty($customRow['customization_data'])) {
                    $jsonData = json_decode($customRow['customization_data'], true);
                    if (is_array($jsonData)) {
                        $jsonData['presta_customization_id'] = $prestaCustomizationId;
                        Db::getInstance()->update('art_puzzle_customization', [
                            'customization_data' => json_encode($jsonData),
                        ], 'id_customization = ' . (int)$customization_id);
                    }
                }
            }

            // Conta prodotti nel carrello
            $cart_products = $this->context->cart->getProducts();
            $cart_count = 0;
            foreach ($cart_products as $p) {
                $cart_count += (int)$p['quantity'];
            }
            
            $response['success'] = true;
            $response['message'] = 'Prodotto aggiunto al carrello';
            $response['data'] = [
                'cart_id' => $this->context->cart->id,
                'cart_count' => $cart_count,
                'cart_url' => $this->context->link->getPageLink('cart', true, null, ['action' => 'show']),
                'product_name' => $product->name,
                'format' => $format,
                'customization_id' => $customization_id,
                'presta_customization_id' => $prestaCustomizationId,
                'price_tax_incl' => isset($customRow['price']) ? (float)$customRow['price'] : 0,
                'price_tax_excl' => isset($customRow['price_tax_excl']) ? (float)$customRow['price_tax_excl'] : 0,
            ];
        } else {
            $response['message'] = 'Errore nell\'aggiunta al carrello';
        }

        die(json_encode($response));
    }
    
    /**
     * Controlla permessi directory
     */
    protected function checkDirectoryPermissions()
    {
        $module_dir = _PS_MODULE_DIR_ . 'art_puzzle/';
        $directories = [
            'upload' => $module_dir . 'upload/',
            'logs' => $module_dir . 'logs/'
        ];
        
        $results = [];
        foreach ($directories as $name => $path) {
            $results[$name] = [
                'exists' => is_dir($path),
                'writable' => is_writable($path)
            ];
        }
        
        $response = [
            'success' => true,
            'data' => $results
        ];
        
        die(json_encode($response));
    }
    
    public function isXmlHttpRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
}
