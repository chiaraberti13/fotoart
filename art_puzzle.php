<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/autoload.php';

use PrestaShop\PrestaShop\Core\Product\ProductExtraContent;
use ArtPuzzle\ArtPuzzleLogger;

class Art_Puzzle extends Module
{
    private const BOOLEAN_CONFIGURATION_KEYS = [
        'ART_PUZZLE_SEND_PREVIEW_USER_EMAIL',
        'ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL',
        'ART_PUZZLE_ENABLE_ORIENTATION',
        'ART_PUZZLE_ENABLE_CROP_TOOL',
        'ART_PUZZLE_ENABLE_PDF_USER',
        'ART_PUZZLE_ENABLE_PDF_ADMIN',
    ];

    private const DEFAULT_BOX_COLORS = [
        ['box' => '#FFFFFF', 'text' => '#000000'],
        ['box' => '#000000', 'text' => '#FFFFFF'],
        ['box' => '#FF0000', 'text' => '#FFFFFF'],
        ['box' => '#0000FF', 'text' => '#FFFFFF'],
    ];

    private const HEX_COLOR_PATTERN = '/^#[0-9A-Fa-f]{6}$/';

    public function __construct()
    {
        $this->name = 'art_puzzle';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Chiara Berti';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7.6.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('Art Puzzle');
        $this->description = $this->l('Modulo PrestaShop per la creazione di puzzle personalizzati.');
        $this->confirmUninstall = $this->l('Sei sicuro di voler disinstallare questo modulo?');
    }

    public function install()
    {
        // Crea le directory necessarie
        $this->createRequiredDirectories();

        $defaultConfiguration = $this->getConfigurationDefaultValues();

        if (!$this->validateConfigurationValues($defaultConfiguration)) {
            ArtPuzzleLogger::log('Valori di default della configurazione non validi durante l\'installazione.', 'ERROR');

            return false;
        }

        // Assicurati che tutti gli hook necessari siano registrati
        return parent::install() &&
            $this->registerHook('displayProductButtons') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayProductExtraContent') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle') &&
            $this->registerHook('displayShoppingCartFooter') &&
            $this->registerHook('actionCartSave') &&
            $this->registerHook('actionOrderStatusPostUpdate') &&
            $this->registerHook('displayOrderConfirmation') &&
            $this->registerHook('actionValidateOrder') &&          // NUOVO
            $this->registerHook('actionPaymentConfirmation') &&    // NUOVO
            $this->initializeConfiguration($defaultConfiguration) &&
            $this->ensureCustomizationTableIndexes();
    }

    private function createRequiredDirectories()
    {
        // Crea directory upload
        $uploadDir = _PS_MODULE_DIR_ . $this->name . '/upload/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Crea directory logs
        $logDir = _PS_MODULE_DIR_ . $this->name . '/logs/';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Crea directory fonts se non esiste
        $fontsDir = _PS_MODULE_DIR_ . $this->name . '/views/fonts/';
        if (!file_exists($fontsDir)) {
            mkdir($fontsDir, 0755, true);
        }
        
        // Crea file di log iniziale se non esiste
        $logFile = $logDir . 'art_puzzle.log';
        if (!file_exists($logFile)) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - [INFO] Modulo Art Puzzle installato\n");
        }
        
        // Assicurati che i permessi siano corretti
        chmod($uploadDir, 0755);
        chmod($logDir, 0755);
        
        return true;
    }

    /**
     * Garantisce la presenza degli indici necessari sulla tabella di personalizzazione del modulo.
     *
     * @return bool
     */
    private function ensureCustomizationTableIndexes()
    {
        $db = Db::getInstance();
        $tableName = _DB_PREFIX_ . 'art_puzzle_customization';

        $tableExists = $db->executeS("SHOW TABLES LIKE '" . pSQL($tableName) . "'");
        if (empty($tableExists)) {
            return true;
        }

        $indexes = [
            'idx_id_product' => 'id_product',
            'idx_id_cart' => 'id_cart',
            'idx_id_order' => 'id_order',
        ];

        foreach ($indexes as $indexName => $column) {
            $query = sprintf(
                "SHOW INDEX FROM `%s` WHERE Key_name = '%s'",
                pSQL($tableName),
                pSQL($indexName)
            );

            $exists = $db->getRow($query);
            if (!$exists) {
                $sql = sprintf(
                    "ALTER TABLE `%s` ADD INDEX `%s` (`%s`)",
                    pSQL($tableName),
                    pSQL($indexName),
                    pSQL($column)
                );

                if (!$db->execute($sql)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        $success = true;

        foreach (array_keys($this->getConfigurationDefaultValues()) as $configurationKey) {
            $success &= Configuration::deleteByName($configurationKey);
        }

        return (bool) $success;
    }

    public function getConfigurationDefaultValues()
    {
        $adminEmail = Configuration::get('PS_SHOP_EMAIL');
        if (!is_string($adminEmail) || !Validate::isEmail($adminEmail)) {
            $adminEmail = '';
        }

        return [
            'ART_PUZZLE_PRODUCT_IDS' => '',
            'ART_PUZZLE_MAX_UPLOAD_SIZE' => '20',
            'ART_PUZZLE_ALLOWED_FILE_TYPES' => 'jpg,jpeg,png',
            'ART_PUZZLE_UPLOAD_FOLDER' => '/upload/',
            'ART_PUZZLE_SEND_PREVIEW_USER_EMAIL' => 1,
            'ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL' => 1,
            'ART_PUZZLE_DEFAULT_BOX_TEXT' => 'Il mio puzzle',
            'ART_PUZZLE_MAX_BOX_TEXT_LENGTH' => '30',
            'ART_PUZZLE_ENABLE_ORIENTATION' => 1,
            'ART_PUZZLE_ENABLE_CROP_TOOL' => 1,
            'ART_PUZZLE_ENABLE_PDF_USER' => 1,
            'ART_PUZZLE_ENABLE_PDF_ADMIN' => 1,
            'ART_PUZZLE_BOX_COLORS' => json_encode(self::DEFAULT_BOX_COLORS),
            'ART_PUZZLE_ADMIN_EMAIL' => $adminEmail,
            'ART_PUZZLE_FONTS' => '',
        ];
    }

    public function validateConfigurationValues(array $values)
    {
        foreach ($values as $key => $value) {
            if (!$this->validateConfigurationValue($key, $value)) {
                return false;
            }
        }

        return true;
    }

    public function validateConfigurationValue($key, $value)
    {
        if (in_array($key, self::BOOLEAN_CONFIGURATION_KEYS, true)) {
            return in_array($value, [0, 1, '0', '1'], true);
        }

        switch ($key) {
            case 'ART_PUZZLE_PRODUCT_IDS':
                if ($value === '' || $value === null) {
                    return true;
                }

                if (!is_string($value)) {
                    return false;
                }

                $ids = array_filter(array_map('trim', explode(',', $value)), 'strlen');
                foreach ($ids as $id) {
                    if (!ctype_digit($id) || (int) $id <= 0) {
                        return false;
                    }
                }

                return true;

            case 'ART_PUZZLE_MAX_UPLOAD_SIZE':
                return is_numeric($value) && (int) $value > 0;

            case 'ART_PUZZLE_ALLOWED_FILE_TYPES':
                if (!is_string($value) || $value === '') {
                    return false;
                }

                $extensions = array_filter(array_map('trim', explode(',', strtolower($value))));
                if (empty($extensions)) {
                    return false;
                }

                foreach ($extensions as $extension) {
                    if (!preg_match('/^[a-z0-9]+$/', $extension)) {
                        return false;
                    }
                }

                return true;

            case 'ART_PUZZLE_UPLOAD_FOLDER':
                return is_string($value) && $value !== '' && strpos($value, '..') === false;

            case 'ART_PUZZLE_DEFAULT_BOX_TEXT':
                return is_string($value);

            case 'ART_PUZZLE_MAX_BOX_TEXT_LENGTH':
                return is_numeric($value) && (int) $value > 0;

            case 'ART_PUZZLE_BOX_COLORS':
                if (!is_string($value)) {
                    return false;
                }

                $decoded = json_decode($value, true);

                if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                    return false;
                }

                if (!is_array($decoded)) {
                    return false;
                }

                foreach ($decoded as $colorSet) {
                    if (!is_array($colorSet) || !isset($colorSet['box'], $colorSet['text'])) {
                        return false;
                    }

                    foreach (['box', 'text'] as $colorKey) {
                        if (!is_string($colorSet[$colorKey]) || !preg_match(self::HEX_COLOR_PATTERN, $colorSet[$colorKey])) {
                            return false;
                        }
                    }
                }

                return true;

            case 'ART_PUZZLE_ADMIN_EMAIL':
                return $value === '' || (is_string($value) && Validate::isEmail($value));

            case 'ART_PUZZLE_FONTS':
                if ($value === '' || $value === null) {
                    return true;
                }

                if (!is_string($value)) {
                    return false;
                }

                $fonts = array_filter(array_map('trim', explode(',', $value)), 'strlen');
                foreach ($fonts as $font) {
                    if (!Validate::isFileName($font)) {
                        return false;
                    }
                }

                return true;
        }

        return true;
    }

    private function initializeConfiguration(array $values)
    {
        foreach ($values as $key => $value) {
            if (!Configuration::updateValue($key, $value)) {
                return false;
            }
        }

        return true;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitArtPuzzle')) {
            $errors = [];

            // Elaborazione degli ID prodotto inviati (vengono inviati come array)
            $product_ids_input = Tools::getValue('ART_PUZZLE_PRODUCT_IDS', []);
            $product_ids_clean = [];
            if (is_array($product_ids_input)) {
                foreach ($product_ids_input as $product_id) {
                    $product_id = trim((string) $product_id);
                    if ($product_id === '') {
                        continue;
                    }

                    if (!Validate::isUnsignedId($product_id)) {
                        $errors[] = $this->l('Gli ID prodotto devono contenere solo numeri positivi.');
                        break;
                    }

                    $product_ids_clean[] = $product_id;
                }
            } elseif ($product_ids_input !== '' && !Validate::isUnsignedId($product_ids_input)) {
                $errors[] = $this->l('Gli ID prodotto devono contenere solo numeri positivi.');
            } elseif ($product_ids_input !== '') {
                $product_ids_clean[] = trim((string) $product_ids_input);
            }
            $product_ids = implode(',', $product_ids_clean);

            $max_upload_size = Tools::getValue('ART_PUZZLE_MAX_UPLOAD_SIZE');
            if ($max_upload_size === null || $max_upload_size === '' || !Validate::isUnsignedInt($max_upload_size) || (int) $max_upload_size <= 0) {
                $errors[] = $this->l('La dimensione massima di upload deve essere un numero intero positivo.');
            } else {
                $max_upload_size = (int) $max_upload_size;
            }

            $allowed_file_types_raw = Tools::getValue('ART_PUZZLE_ALLOWED_FILE_TYPES');
            $allowed_file_types_list = [];
            if ($allowed_file_types_raw === null || trim((string) $allowed_file_types_raw) === '') {
                $errors[] = $this->l('Inserisci almeno un formato file consentito.');
            } else {
                $candidate_types = array_filter(array_map('trim', explode(',', (string) $allowed_file_types_raw)));
                if (empty($candidate_types)) {
                    $errors[] = $this->l('Inserisci almeno un formato file consentito.');
                } else {
                    foreach ($candidate_types as $candidate) {
                        if (!preg_match('/^[a-z0-9]+$/i', $candidate)) {
                            $errors[] = $this->l('I formati file consentiti possono contenere solo lettere e numeri, separati da virgola.');
                            break;
                        }
                        $allowed_file_types_list[] = Tools::strtolower($candidate);
                    }
                }
            }
            $allowed_file_types = implode(',', $allowed_file_types_list);

            $upload_folder = Tools::getValue('ART_PUZZLE_UPLOAD_FOLDER');
            $upload_folder = $upload_folder !== null ? trim((string) $upload_folder) : '';
            if ($upload_folder === '') {
                $errors[] = $this->l('La cartella di upload non può essere vuota.');
            } elseif (!Validate::isCleanHtml($upload_folder)) {
                $errors[] = $this->l('La cartella di upload contiene caratteri non validi.');
            } else {
                if (substr($upload_folder, 0, 1) !== '/') {
                    $upload_folder = '/' . $upload_folder;
                }
                if (substr($upload_folder, -1) !== '/') {
                    $upload_folder .= '/';
                }
            }

            $booleanFields = [
                'ART_PUZZLE_SEND_PREVIEW_USER_EMAIL' => $this->l('Valore non valido per l\'opzione di invio email all\'utente.'),
                'ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL' => $this->l('Valore non valido per l\'opzione di invio email all\'amministratore.'),
                'ART_PUZZLE_ENABLE_ORIENTATION' => $this->l('Valore non valido per l\'opzione di orientamento.'),
                'ART_PUZZLE_ENABLE_CROP_TOOL' => $this->l('Valore non valido per l\'opzione di ritaglio.'),
                'ART_PUZZLE_ENABLE_PDF_USER' => $this->l('Valore non valido per l\'opzione PDF utente.'),
                'ART_PUZZLE_ENABLE_PDF_ADMIN' => $this->l('Valore non valido per l\'opzione PDF amministratore.'),
            ];
            $booleanValues = [];
            foreach ($booleanFields as $field => $errorMessage) {
                $rawValue = Tools::getValue($field, null);

                if ($rawValue === null) {
                    $value = 0;
                } elseif (in_array($rawValue, ['on', 'true'], true)) {
                    $value = 1;
                } elseif (in_array($rawValue, ['off', 'false'], true)) {
                    $value = 0;
                } else {
                    $value = (int) $rawValue;
                }

                if (!in_array($value, [0, 1], true)) {
                    $errors[] = $errorMessage;
                }

                $booleanValues[$field] = $value;
            }

            $default_box_text = Tools::getValue('ART_PUZZLE_DEFAULT_BOX_TEXT', '');
            if ($default_box_text !== '' && !Validate::isCleanHtml($default_box_text)) {
                $errors[] = $this->l('Il testo predefinito della scatola contiene caratteri non validi.');
            }

            $max_box_text_length = Tools::getValue('ART_PUZZLE_MAX_BOX_TEXT_LENGTH');
            if ($max_box_text_length === null || $max_box_text_length === '' || !Validate::isUnsignedInt($max_box_text_length) || (int) $max_box_text_length <= 0) {
                $errors[] = $this->l('La lunghezza massima del testo deve essere un numero intero positivo.');
            } else {
                $max_box_text_length = (int) $max_box_text_length;
            }

            $admin_email = trim((string) Tools::getValue('ART_PUZZLE_ADMIN_EMAIL'));
            if ($admin_email === '' || !Validate::isEmail($admin_email)) {
                $errors[] = $this->l('Inserisci un indirizzo email amministratore valido.');
            }

            $box_colors_raw = Tools::getValue('ART_PUZZLE_BOX_COLORS', []);
            $box_colors_clean = [];
            if (is_array($box_colors_raw)) {
                foreach ($box_colors_raw as $color_json) {
                    $color_set = json_decode($color_json, true);
                    if (!$color_set || !isset($color_set['box'], $color_set['text'])) {
                        $errors[] = $this->l('Una delle combinazioni di colori non è valida.');
                        break;
                    }

                    if (!$this->isValidHexColor($color_set['box']) || !$this->isValidHexColor($color_set['text'])) {
                        $errors[] = $this->l('I colori devono essere in formato esadecimale a 6 caratteri.');
                        break;
                    }

                    $box_colors_clean[] = [
                        'box' => Tools::strtoupper($color_set['box']),
                        'text' => Tools::strtoupper($color_set['text']),
                    ];
                }
            } else {
                $errors[] = $this->l('Una delle combinazioni di colori non è valida.');
            }

            $fonts_to_upload = [];
            for ($i = 1; $i <= 10; $i++) {
                if (isset($_FILES['ART_PUZZLE_FONT_' . $i]) && !empty($_FILES['ART_PUZZLE_FONT_' . $i]['name'])) {
                    $file = $_FILES['ART_PUZZLE_FONT_' . $i];
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    if (Tools::strtolower($ext) !== 'ttf') {
                        $errors[] = $this->l('I file dei font devono essere in formato TTF.');
                        break;
                    }
                    $fonts_to_upload[$i] = $file;
                }
            }

            if (empty($errors)) {
                Configuration::updateValue('ART_PUZZLE_PRODUCT_IDS', $product_ids);
                Configuration::updateValue('ART_PUZZLE_MAX_UPLOAD_SIZE', (string) $max_upload_size);
                Configuration::updateValue('ART_PUZZLE_ALLOWED_FILE_TYPES', $allowed_file_types);
                Configuration::updateValue('ART_PUZZLE_UPLOAD_FOLDER', $upload_folder);
                Configuration::updateValue('ART_PUZZLE_SEND_PREVIEW_USER_EMAIL', $booleanValues['ART_PUZZLE_SEND_PREVIEW_USER_EMAIL']);
                Configuration::updateValue('ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL', $booleanValues['ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL']);
                Configuration::updateValue('ART_PUZZLE_DEFAULT_BOX_TEXT', $default_box_text);
                Configuration::updateValue('ART_PUZZLE_MAX_BOX_TEXT_LENGTH', (string) $max_box_text_length);
                Configuration::updateValue('ART_PUZZLE_ENABLE_ORIENTATION', $booleanValues['ART_PUZZLE_ENABLE_ORIENTATION']);
                Configuration::updateValue('ART_PUZZLE_ENABLE_CROP_TOOL', $booleanValues['ART_PUZZLE_ENABLE_CROP_TOOL']);
                Configuration::updateValue('ART_PUZZLE_ENABLE_PDF_USER', $booleanValues['ART_PUZZLE_ENABLE_PDF_USER']);
                Configuration::updateValue('ART_PUZZLE_ENABLE_PDF_ADMIN', $booleanValues['ART_PUZZLE_ENABLE_PDF_ADMIN']);
                Configuration::updateValue('ART_PUZZLE_ADMIN_EMAIL', $admin_email);
                Configuration::updateValue('ART_PUZZLE_BOX_COLORS', json_encode($box_colors_clean));

                // Gestione del caricamento dei file dei fonts (massimo 10 file)
                if (!empty($fonts_to_upload)) {
                    $upload_folder_path = _PS_MODULE_DIR_ . $this->name . '/views/fonts/';
                    if (!file_exists($upload_folder_path)) {
                        mkdir($upload_folder_path, 0755, true);
                    }

                    $uploaded_fonts = [];
                    $current_fonts = Configuration::get('ART_PUZZLE_FONTS');
                    if ($current_fonts) {
                        $uploaded_fonts = explode(',', $current_fonts);
                    }

                    foreach ($fonts_to_upload as $index => $file) {
                        $new_filename = 'font_' . $index . '.ttf';
                        if (move_uploaded_file($file['tmp_name'], $upload_folder_path . $new_filename)) {
                            if (!in_array($new_filename, $uploaded_fonts)) {
                                $uploaded_fonts[] = $new_filename;
                            }
                        }
                    }

                    if (count($uploaded_fonts) > 0) {
                        Configuration::updateValue('ART_PUZZLE_FONTS', implode(',', $uploaded_fonts));
                    }
                }

                $output .= $this->displayConfirmation($this->l('Impostazioni aggiornate'));

                // Verifica e crea directory se necessario
                $this->createRequiredDirectories();
            } else {
                foreach ($errors as $error) {
                    $output .= $this->displayError($error);
                }
            }
        }

        return $output . $this->displayForm();
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        // Aggiungi variabili JavaScript globali necessarie
        if ($this->shouldLoadPuzzleAssets()) {
            Media::addJsDef([
                'artPuzzleAjaxUrl' => $this->context->link->getModuleLink('art_puzzle', 'ajax', [], true),
                'artPuzzleToken' => Tools::getToken(false),
                'artPuzzleUploadUrl' => $this->context->link->getModuleLink('art_puzzle', 'upload', [], true),
                'artPuzzleMaxFileSize' => (int)Configuration::get('ART_PUZZLE_MAX_UPLOAD_SIZE', 20) * 1024 * 1024,
                'artPuzzleDebug' => _PS_MODE_DEV_
            ]);
        }

        if (!$this->shouldLoadPuzzleAssets()) {
            return;
        }

        $product_id = (int)Tools::getValue('id_product');
        $controller = $this->context->controller;

        if (!$controller) {
            return;
        }

        $controller->registerJavascript(
            'module-art-puzzle-front',
            'modules/' . $this->name . '/views/js/front.js',
            ['position' => 'bottom', 'priority' => 150]
        );

        $controller->registerStylesheet(
            'module-art-puzzle-front',
            'modules/' . $this->name . '/views/css/front.css',
            ['media' => 'all', 'priority' => 150]
        );

        if (Configuration::get('ART_PUZZLE_ENABLE_CROP_TOOL')) {
            $controller->registerJavascript(
                'module-art-puzzle-cropper',
                'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js',
                ['server' => 'remote', 'position' => 'bottom', 'priority' => 140]
            );

            $controller->registerStylesheet(
                'module-art-puzzle-cropper',
                'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css',
                ['server' => 'remote', 'media' => 'all', 'priority' => 140]
            );
        }

        $this->registerCustomFontsStylesheet($controller);

        Media::addJsDef([
            'art_puzzle_ajax_url' => $this->context->link->getModuleLink('art_puzzle', 'ajax'),
            'art_puzzle_product_id' => $product_id,
            'art_puzzle_token' => Tools::getToken(false),
        ]);

    }

    public function registerCustomFontsStylesheet($controller)
    {
        $fonts = Configuration::get('ART_PUZZLE_FONTS');
        if (!$fonts) {
            return;
        }

        $fonts_array = explode(',', $fonts);
        $css = '';

        foreach ($fonts_array as $index => $font) {
            $font_url = $this->_path . 'views/fonts/' . $font;
            $css .= '@font-face {
                font-family: "puzzle-font-' . $index . '";
                src: url("' . $font_url . '") format("truetype");
                font-weight: normal;
                font-style: normal;
            }';
        }

        if ($css !== '') {
            $controller->registerStylesheet(
                'module-art-puzzle-fonts',
                null,
                [
                    'inline' => true,
                    'media' => 'all',
                    'priority' => 155,
                    'content' => $css,
                ]
            );
        }
    }

    public function hookDisplayProductExtraContent($params)
    {
        // Fixed version - compatibile con PrestaShop 1.7.6
        try {
            if (!isset($params['product']) || !$params['product']) {
                return [];
            }
            
            $id_product = is_object($params['product']) ? $params['product']->id : $params['product']['id'];
            
            if (!$this->isPuzzleProduct($id_product)) {
                return [];
            }
            
            // Verifica classe ProductExtraContent
            $extraContentClass = 'PrestaShop\PrestaShop\Core\Product\ProductExtraContent';
            if (!class_exists($extraContentClass)) {
                // Fallback per versioni precedenti di PrestaShop
                // Semplicemente non mostra il tab
                return [];
            }
            
            // Assicurati che $link sia disponibile
            if (!isset($this->context->link) || !$this->context->link) {
                $this->context->link = new Link();
            }
            
            // Prepara le variabili per il template
            $this->context->smarty->assign([
                'id_product' => $id_product,
                'puzzleAjaxUrl' => $this->context->link->getModuleLink('art_puzzle', 'ajax', [], true),
                'securityToken' => Tools::getToken(false),
                'upload_max_size' => Configuration::get('ART_PUZZLE_MAX_UPLOAD_SIZE'),
                'allowed_file_types' => explode(',', Configuration::get('ART_PUZZLE_ALLOWED_FILE_TYPES')),
                'enable_orientation' => Configuration::get('ART_PUZZLE_ENABLE_ORIENTATION'),
                'enable_crop_tool' => Configuration::get('ART_PUZZLE_ENABLE_CROP_TOOL')
            ]);
            
            $extraContent = new $extraContentClass();
            
            // Usa i metodi in modo sicuro
            if (method_exists($extraContent, 'setTitle')) {
                $extraContent->setTitle($this->l('Personalizza il tuo puzzle'));
            }
            
            // Il metodo setType potrebbe non esistere in alcune versioni
            if (method_exists($extraContent, 'setType') && defined($extraContentClass . '::TYPE_TAB')) {
                $extraContent->setType($extraContentClass::TYPE_TAB);
            }
            
            if (method_exists($extraContent, 'setContent')) {
                $content = $this->display(__FILE__, 'views/templates/hook/displayProductExtraContent.tpl');
                $extraContent->setContent($content);
            }
            
            return [$extraContent];
            
        } catch (Exception $e) {
            // Log error but don't break the page
            if (class_exists('ArtPuzzle\ArtPuzzleLogger')) {
                \ArtPuzzle\ArtPuzzleLogger::log('Error in hookDisplayProductExtraContent: ' . $e->getMessage(), 'ERROR');
            }
            return [];
        }
    }

    public function hookDisplayProductButtons($params)
    {
        // Questa funzione mostra contenuto sotto i pulsanti del prodotto
        if (isset($params['product']) && $this->isPuzzleProduct($params['product']['id_product'])) {
            $this->context->smarty->assign([
                'id_product' => $params['product']['id_product']
            ]);
            
            return $this->display(__FILE__, 'views/templates/hook/displayProductButtons.tpl');
        }
        
        return '';
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        // Aggiungi CSS e JS per il backoffice
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addJS($this->_path . 'views/js/admin.js');
        }
    }

    /**
     * Hook per la visualizzazione nei tab del prodotto nel backoffice
     */
public function hookDisplayAdminProductsExtra($params)
{
    if (!isset($params['id_product'])) {
        return '';
    }
    
    $id_product = (int)$params['id_product'];
    
    // Ottieni i prodotti configurati per l'uso con Art Puzzle
    $product_ids_str = Configuration::get('ART_PUZZLE_PRODUCT_IDS');
    $product_ids_array = $product_ids_str ? explode(',', $product_ids_str) : [];
    
    $is_puzzle_product = in_array((string)$id_product, $product_ids_array);
    
    $this->context->smarty->assign([
        'id_product' => $id_product,
        'is_puzzle_product' => $is_puzzle_product,
        'module_dir' => $this->_path,
        'module_token' => Tools::getAdminTokenLite('AdminModules'),
        'ajax_url' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name]),
    ]);
    
    return $this->display(__FILE__, 'views/templates/admin/product_tab.tpl');
}

    public function isPuzzleProduct($id_product)
    {
        $id_product = (int) $id_product;
        if ($id_product <= 0) {
            return false;
        }

        $product_ids = $this->getConfiguredPuzzleProductIds();

        return in_array((string) $id_product, $product_ids, true);
    }

    public function shouldLoadPuzzleAssets()
    {
        $controller = $this->context->controller;

        if (!$controller || !property_exists($controller, 'php_self')) {
            return false;
        }

        if ($controller->php_self !== 'product') {
            return false;
        }

        $product_id = (int) Tools::getValue('id_product');

        return $product_id > 0 && $this->isPuzzleProduct($product_id);
    }

    public function getConfiguredPuzzleProductIds()
    {
        $product_ids_str = Configuration::get('ART_PUZZLE_PRODUCT_IDS');
        if (!$product_ids_str) {
            return [];
        }

        $ids = array_map('trim', explode(',', $product_ids_str));

        return array_values(array_filter($ids, static function ($id) {
            return $id !== '';
        }));
    }

public function hookDisplayShoppingCartFooter($params)
{
    $cart = $this->context->cart;
    $products = $cart->getProducts();
    $customized_items = [];
    
    foreach ($products as $product) {
        $customizations = $cart->getProductCustomization($product['id_product']);
        
        if ($customizations) {
            foreach ($customizations as $cust) {
                if (isset($cust['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                    foreach ($cust['datas'][Product::CUSTOMIZE_TEXTFIELD] as $custom_text) {
                        if ($custom_text['name'] === 'art_puzzle_data') {
                            $data = json_decode($custom_text['value'], true);
                            $customized_items[] = [
                                'product_name' => $product['name'],
                                'box_text' => $data['box_text'] ?? '',
                                'format_name' => $data['format'] ?? '',
                                'box_preview' => isset($data['image']) ? $this->getPathUri() . 'upload/' . $data['image'] : '',
                                'edit_url' => '', // opzionale: link alla modifica
                            ];
                        }
                    }
                }
            }
        }
    }
    
    $this->context->smarty->assign('art_puzzle_items', $customized_items);
    return $this->display(__FILE__, 'views/templates/hook/displayShoppingCartFooter.tpl');
}

    private function displayForm()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form = [];

        // Sezione ID Prodotti Puzzle
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Prodotti Puzzle')
            ],
            'input' => [
                [
                    'type' => 'html',
                    'name' => 'product_ids_container',
                    'html_content' => $this->getProductIdsHtml(),
                ],
            ],
        ];

        // Sezione Upload Immagini
        $fields_form[1]['form'] = [
            'legend' => [
                'title' => $this->l('Configurazione Upload')
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Dimensione massima upload (MB)'),
                    'name' => 'ART_PUZZLE_MAX_UPLOAD_SIZE',
                    'desc' => $this->l('Imposta la dimensione massima per l\'upload in MB'),
                    'class' => 'fixed-width-sm',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Formati immagini consentiti'),
                    'name' => 'ART_PUZZLE_ALLOWED_FILE_TYPES',
                    'desc' => $this->l('Inserisci i formati consentiti separati da virgola (es. jpg,png)'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Cartella di upload immagini'),
                    'name' => 'ART_PUZZLE_UPLOAD_FOLDER',
                    'desc' => $this->l('Cartella dove verranno salvate le immagini'),
                ],
            ],
        ];

        // Sezione Notifiche Email e PDF
        $fields_form[2]['form'] = [
            'legend' => [
                'title' => $this->l('Notifiche Email e PDF')
            ],
            'input' => [
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Invia preview a utente via email'),
                    'name'    => 'ART_PUZZLE_SEND_PREVIEW_USER_EMAIL',
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Sì')
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ]
                    ],
                ],
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Invia preview a admin via email'),
                    'name'    => 'ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL',
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Sì')
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ]
                    ],
                ],
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Abilita PDF per utente'),
                    'name'    => 'ART_PUZZLE_ENABLE_PDF_USER',
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Sì')
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ]
                    ],
                ],
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Abilita PDF per admin'),
                    'name'    => 'ART_PUZZLE_ENABLE_PDF_ADMIN',
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Sì')
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ]
                    ],
                ],
                [
                    'type'        => 'text',
                    'label'       => $this->l('Email amministratore'),
                    'name'        => 'ART_PUZZLE_ADMIN_EMAIL',
                    'placeholder' => $this->l('email amministratore'),
                    'desc'        => $this->l('Inserisci l\'indirizzo email per le notifiche amministrative'),
                ],
            ],
        ];

        // Sezione Personalizzazione Scatola
        $fields_form[3]['form'] = [
            'legend' => [
                'title' => $this->l('Personalizzazione Scatola')
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Testo predefinito scatola'),
                    'name' => 'ART_PUZZLE_DEFAULT_BOX_TEXT',
                    'desc' => $this->l('Inserisci il testo predefinito per la scatola'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Lunghezza massima testo scatola'),
                    'name' => 'ART_PUZZLE_MAX_BOX_TEXT_LENGTH',
                    'desc' => $this->l('Imposta la lunghezza massima del testo (es. 30)'),
                    'class' => 'fixed-width-sm',
                ],
                [
                    'type' => 'html',
                    'name' => 'box_colors_container',
                    'html_content' => $this->getBoxColorsHtml(),
                ],
            ],
        ];

        // Sezione Font Personalizzati
        $fields_form[4]['form'] = [
            'legend' => [
                'title' => $this->l('Font Personalizzati')
            ],
            'input' => [
                [
                    'type' => 'html',
                    'name' => 'fonts_container',
                    'html_content' => $this->getFontsHtml(),
                ],
            ],
        ];

        // Sezione Funzionalità
        $fields_form[5]['form'] = [
            'legend' => [
                'title' => $this->l('Funzionalità')
            ],
            'input' => [
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Abilita orientamento'),
                    'name'    => 'ART_PUZZLE_ENABLE_ORIENTATION',
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Sì')
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ]
                    ],
                ],
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Abilita crop interattivo'),
                    'name'    => 'ART_PUZZLE_ENABLE_CROP_TOOL',
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Sì')
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ]
                    ],
                ],
            ],
            'submit'  => [
                'title' => $this->l('Salva')
            ],
            'enctype' => 'multipart/form-data'
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitArtPuzzle';

        // Impostazione dei valori correnti per ciascun campo
        $helper->fields_value['ART_PUZZLE_MAX_UPLOAD_SIZE'] = Tools::getValue('ART_PUZZLE_MAX_UPLOAD_SIZE', Configuration::get('ART_PUZZLE_MAX_UPLOAD_SIZE'));
        $helper->fields_value['ART_PUZZLE_ALLOWED_FILE_TYPES'] = Tools::getValue('ART_PUZZLE_ALLOWED_FILE_TYPES', Configuration::get('ART_PUZZLE_ALLOWED_FILE_TYPES'));
        $helper->fields_value['ART_PUZZLE_UPLOAD_FOLDER'] = Tools::getValue('ART_PUZZLE_UPLOAD_FOLDER', Configuration::get('ART_PUZZLE_UPLOAD_FOLDER'));
        $helper->fields_value['ART_PUZZLE_SEND_PREVIEW_USER_EMAIL'] = Tools::getValue('ART_PUZZLE_SEND_PREVIEW_USER_EMAIL', Configuration::get('ART_PUZZLE_SEND_PREVIEW_USER_EMAIL'));
        $helper->fields_value['ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL'] = Tools::getValue('ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL', Configuration::get('ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL'));
        $helper->fields_value['ART_PUZZLE_DEFAULT_BOX_TEXT'] = Tools::getValue('ART_PUZZLE_DEFAULT_BOX_TEXT', Configuration::get('ART_PUZZLE_DEFAULT_BOX_TEXT'));
        $helper->fields_value['ART_PUZZLE_MAX_BOX_TEXT_LENGTH'] = Tools::getValue('ART_PUZZLE_MAX_BOX_TEXT_LENGTH', Configuration::get('ART_PUZZLE_MAX_BOX_TEXT_LENGTH'));
        $helper->fields_value['ART_PUZZLE_ENABLE_ORIENTATION'] = Tools::getValue('ART_PUZZLE_ENABLE_ORIENTATION', Configuration::get('ART_PUZZLE_ENABLE_ORIENTATION'));
        $helper->fields_value['ART_PUZZLE_ENABLE_CROP_TOOL'] = Tools::getValue('ART_PUZZLE_ENABLE_CROP_TOOL', Configuration::get('ART_PUZZLE_ENABLE_CROP_TOOL'));
        $helper->fields_value['ART_PUZZLE_ENABLE_PDF_USER'] = Tools::getValue('ART_PUZZLE_ENABLE_PDF_USER', Configuration::get('ART_PUZZLE_ENABLE_PDF_USER'));
        $helper->fields_value['ART_PUZZLE_ENABLE_PDF_ADMIN'] = Tools::getValue('ART_PUZZLE_ENABLE_PDF_ADMIN', Configuration::get('ART_PUZZLE_ENABLE_PDF_ADMIN'));
        $helper->fields_value['ART_PUZZLE_ADMIN_EMAIL'] = Tools::getValue('ART_PUZZLE_ADMIN_EMAIL', Configuration::get('ART_PUZZLE_ADMIN_EMAIL'));

        return $helper->generateForm($fields_form) . $this->addJs();
    }

    private function getProductIdsHtml()
    {
        $current_product_ids = Configuration::get('ART_PUZZLE_PRODUCT_IDS');
        $ids_array = $current_product_ids ? explode(',', $current_product_ids) : [];
        
        $html = '<div id="product_ids_container">';
        
        // Prima riga per aggiungere un nuovo ID
        $html .= '<div class="form-group">';
        $html .= '<div class="input-group" style="margin-bottom: 10px;">';
        $html .= '<span class="input-group-addon">ID Prodotto</span>';
        $html .= '<input type="text" id="new_product_id" class="form-control" placeholder="Inserisci un ID prodotto" />';
        $html .= '<span class="input-group-btn">';
        $html .= '<button type="button" class="btn btn-default" onclick="addProductId()">Aggiungi</button>';
        $html .= '</span>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Lista degli ID già caricati
        $html .= '<div id="product_ids_list">';
        if (!empty($ids_array)) {
            $html .= '<p><strong>ID prodotti caricati:</strong></p>';
            foreach ($ids_array as $index => $id) {
                if ($id !== '') {
                    $html .= '<div class="form-group" style="margin-bottom: 10px;">';
                    $html .= '<div class="input-group">';
                    $html .= '<input type="text" name="ART_PUZZLE_PRODUCT_IDS[]" value="' . $id . '" class="form-control" readonly />';
                    $html .= '<span class="input-group-btn">';
                    $html .= '<button type="button" class="btn btn-danger" onclick="removeProductIdField(this)">Rimuovi</button>';
                    $html .= '</span>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            }
        }
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }

    private function getBoxColorsHtml()
    {
        $box_colors = Configuration::get('ART_PUZZLE_BOX_COLORS') ? Configuration::get('ART_PUZZLE_BOX_COLORS') : '[]';
        $colors_array = json_decode($box_colors, true) ?: [];

        $html = '<div id="box_colors_container">';
        
        // Prima riga per aggiungere nuovi colori
        $html .= '<div class="form-group">';
        $html .= '<div class="row" style="margin-bottom: 20px; display: flex; align-items: center;">';
        $html .= '<div class="col-md-5">';
        $html .= '<label style="margin-bottom: 5px; display: block;">Colore scatola</label>';
        $html .= '<div style="display: flex; align-items: center;">';
        $html .= '<input type="color" id="new_box_color" class="form-control" value="#ffffff" style="padding: 3px; height: 38px; width: 60px; margin-right: 10px;" onchange="updateColorPreview(this, \'new_box_color_hex\')" />';
        $html .= '<input type="text" id="new_box_color_hex" class="form-control" value="#ffffff" style="max-width: 100px;" onchange="updateColorFromHex(this, \'new_box_color\')" />';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-5">';
        $html .= '<label style="margin-bottom: 5px; display: block;">Colore testo</label>';
        $html .= '<div style="display: flex; align-items: center;">';
        $html .= '<input type="color" id="new_text_color" class="form-control" value="#000000" style="padding: 3px; height: 38px; width: 60px; margin-right: 10px;" onchange="updateColorPreview(this, \'new_text_color_hex\')" />';
        $html .= '<input type="text" id="new_text_color_hex" class="form-control" value="#000000" style="max-width: 100px;" onchange="updateColorFromHex(this, \'new_text_color\')" />';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-2" style="display: flex; align-items: flex-end; height: 75px;">';
        $html .= '<button type="button" class="btn btn-primary" onclick="addBoxColors()">Aggiungi</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Lista dei colori già caricati
        $html .= '<div id="box_colors_list">';
        if (!empty($colors_array)) {
            $html .= '<p><strong>Combinazioni colori preimpostate:</strong></p>';
            foreach ($colors_array as $index => $color_set) {
                $boxColorContrast = $this->getContrastColor($color_set['box']);
                $textColorContrast = $this->getContrastColor($color_set['text']);
                
                $html .= '<div class="form-group" style="margin-bottom: 10px;">';
                $html .= '<div class="row" style="display: flex; align-items: center;">';
                $html .= '<div class="col-md-10">';
                $html .= '<div style="display: flex; align-items: center; background-color: #f5f5f5; padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px;">';
                $html .= 'Scatola: <span style="background-color:' . $color_set['box'] . '; color:' . $boxColorContrast . '; padding: 2px 10px; border: 1px solid #000; margin: 0 5px;">' . $color_set['box'] . '</span> ';
                $html .= 'Testo: <span style="background-color:' . $color_set['text'] . '; color:' . $textColorContrast . '; padding: 2px 10px; border: 1px solid #000; margin: 0 5px;">' . $color_set['text'] . '</span>';
                $html .= '<input type="hidden" name="ART_PUZZLE_BOX_COLORS[]" value=\'' . json_encode($color_set) . '\' />';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                $html .= '<button type="button" class="btn btn-danger" onclick="removeBoxColors(this)">Rimuovi</button>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }

    private function isValidHexColor($color)
    {
        return is_string($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
    }

    private function getContrastColor($hexColor)
    {
        // Rimuovi il # se presente
        $hex = str_replace('#', '', $hexColor);
        
        // Converti i valori esadecimali in decimali
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Calcola la luminosità
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        // Ritorna bianco per colori scuri, nero per colori chiari
        return ($yiq >= 128) ? '#000000' : '#FFFFFF';
    }

    private function getFontsHtml()
    {
        $current_fonts = Configuration::get('ART_PUZZLE_FONTS');
        $fonts_array = $current_fonts ? explode(',', $current_fonts) : [];
        
        $html = '<div id="fonts_container">';
        
        // Prima riga per aggiungere un nuovo font
        $html .= '<div class="form-group">';
        $html .= '<div class="input-group" style="margin-bottom: 10px;">';
        $html .= '<span class="input-group-addon">Carica Font TTF</span>';
        $html .= '<input type="file" id="new_font_file" class="form-control" accept=".ttf" />';
        $html .= '<span class="input-group-btn">';
        $html .= '<button type="button" class="btn btn-default" onclick="addFontWithFile()">Aggiungi Font</button>';
        $html .= '</span>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Lista dei font già caricati
        $html .= '<div id="fonts_list">';
        if (!empty($fonts_array)) {
            $html .= '<p><strong>Font caricati:</strong></p>';
            foreach ($fonts_array as $index => $font) {
                $html .= '<div class="form-group" style="margin-bottom: 10px;">';
                $html .= '<div class="input-group">';
                $html .= '<span class="form-control">' . $font . '</span>';
                $html .= '<input type="hidden" name="ART_PUZZLE_FONT_' . ($index + 1) . '" value="' . $font . '" />';
                $html .= '<span class="input-group-btn">';
                $html .= '<button type="button" class="btn btn-danger" onclick="removeFontField(this, ' . ($index + 1) . ')">Rimuovi</button>';
                $html .= '</span>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }

    private function addJs()
    {
        $js = '<script type="text/javascript">
            function updateColorPreview(colorInput, hexInputId) {
                document.getElementById(hexInputId).value = colorInput.value;
            }
            
            function updateColorFromHex(hexInput, colorInputId) {
                var hex = hexInput.value;
                if (hex.charAt(0) !== "#") {
                    hex = "#" + hex;
                }
                if (/^#[0-9A-F]{6}$/i.test(hex)) {
                    document.getElementById(colorInputId).value = hex;
                    hexInput.value = hex;
                }
            }
            
            function addProductId() {
                var newId = document.getElementById("new_product_id").value.trim();
                if (newId !== "") {
                    var container = document.getElementById("product_ids_list");
                    
                    // Aggiungi titolo se non esiste ancora
                    if (container.children.length === 0) {
                        var title = document.createElement("p");
                        title.innerHTML = "<strong>ID prodotti caricati:</strong>";
                        container.appendChild(title);
                    }
                    
                    var div = document.createElement("div");
                    div.className = "form-group";
                    div.style.marginBottom = "10px";
                    
                    var inputGroup = document.createElement("div");
                    inputGroup.className = "input-group";
                    
                    var input = document.createElement("input");
                    input.type = "text";
                    input.name = "ART_PUZZLE_PRODUCT_IDS[]";
                    input.value = newId;
                    input.className = "form-control";
                    input.readOnly = true;
                    
                    var buttonSpan = document.createElement("span");
                    buttonSpan.className = "input-group-btn";
                    
                    var removeBtn = document.createElement("button");
                    removeBtn.type = "button";
                    removeBtn.className = "btn btn-danger";
                    removeBtn.innerHTML = "Rimuovi";
                    removeBtn.onclick = function() { removeProductIdField(this); };
                    
                    buttonSpan.appendChild(removeBtn);
                    inputGroup.appendChild(input);
                    inputGroup.appendChild(buttonSpan);
                    div.appendChild(inputGroup);
                    container.appendChild(div);
                    
                    // Pulisci il campo di input
                    document.getElementById("new_product_id").value = "";
                }
            }
            
            function removeProductIdField(btn) {
                var div = btn.parentNode.parentNode.parentNode;
                div.parentNode.removeChild(div);
                
                // Se non ci sono più ID, rimuovi il titolo
                var container = document.getElementById("product_ids_list");
                if (container.children.length <= 1) {
                    container.innerHTML = "";
                }
            }
            
            function getContrastColor(hexColor) {
                var hex = hexColor.replace("#", "");
                var r = parseInt(hex.substr(0, 2), 16);
                var g = parseInt(hex.substr(2, 2), 16);
                var b = parseInt(hex.substr(4, 2), 16);
                var yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return (yiq >= 128) ? "#000000" : "#FFFFFF";
            }
            
            function addBoxColors() {
                var boxColor = document.getElementById("new_box_color").value;
                var textColor = document.getElementById("new_text_color").value;
                
                if (boxColor === "" || textColor === "") {
                    alert("Per favore seleziona entrambi i colori");
                    return;
                }
                
                var container = document.getElementById("box_colors_list");
                
                // Aggiungi titolo se non esiste ancora
                if (container.children.length === 0) {
                    var title = document.createElement("p");
                    title.innerHTML = "<strong>Combinazioni colori preimpostate:</strong>";
                    container.appendChild(title);
                }
                
                var div = document.createElement("div");
                div.className = "form-group";
                div.style.marginBottom = "10px";
                
                var row = document.createElement("div");
                row.className = "row";
                row.style.display = "flex";
                row.style.alignItems = "center";
                
                var col10 = document.createElement("div");
                col10.className = "col-md-10";
                
                var display = document.createElement("div");
                display.style.display = "flex";
                display.style.alignItems = "center";
                display.style.backgroundColor = "#f5f5f5";
                display.style.padding = "6px 12px";
                display.style.border = "1px solid #ccc";
                display.style.borderRadius = "4px";
                
                var boxColorContrast = getContrastColor(boxColor);
                var textColorContrast = getContrastColor(textColor);
                
               display.innerHTML = "Scatola: <span style=\\"background-color:" + boxColor + "; color:" + boxColorContrast + "; padding: 2px 10px; border: 1px solid #000; margin: 0 5px;\\">" + boxColor + "</span> " +
                               "Testo: <span style=\\"background-color:" + textColor + "; color:" + textColorContrast + "; padding: 2px 10px; border: 1px solid #000; margin: 0 5px;\\">" + textColor + "</span>";
                               
                var hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "ART_PUZZLE_BOX_COLORS[]";
                hiddenInput.value = JSON.stringify({box: boxColor, text: textColor});
                
                display.appendChild(hiddenInput);
                col10.appendChild(display);
                
                var col2 = document.createElement("div");
                col2.className = "col-md-2";
                
                var removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.className = "btn btn-danger";
                removeBtn.innerHTML = "Rimuovi";
                removeBtn.onclick = function() { removeBoxColors(this); };
                
                col2.appendChild(removeBtn);
                row.appendChild(col10);
                row.appendChild(col2);
                div.appendChild(row);
                container.appendChild(div);
            }
            
            function removeBoxColors(btn) {
                var div = btn.parentNode.parentNode.parentNode;
                div.parentNode.removeChild(div);
                
                // Se non ci sono più colori, rimuovi il titolo
                var container = document.getElementById("box_colors_list");
                if (container.children.length <= 1) {
                    container.innerHTML = "";
                }
            }
            
            var fontCount = 0;
            function addFontWithFile() {
                var fileInput = document.getElementById("new_font_file");
                if (fileInput.files.length > 0) {
                    var container = document.getElementById("fonts_list");
                    fontCount++;
                    
                    // Aggiungi titolo se non esiste ancora
                    if (container.children.length === 0) {
                        var title = document.createElement("p");
                        title.innerHTML = "<strong>Font caricati:</strong>";
                        container.appendChild(title);
                    }
                    
                    var div = document.createElement("div");
                    div.className = "form-group";
                    div.style.marginBottom = "10px";
                    
                    var inputGroup = document.createElement("div");
                    inputGroup.className = "input-group";
                    
                    var display = document.createElement("span");
                    display.className = "form-control";
                    display.innerHTML = fileInput.files[0].name;
                    
                    var hiddenInput = document.createElement("input");
                    hiddenInput.type = "hidden";
                    hiddenInput.name = "ART_PUZZLE_FONT_" + fontCount;
                    
                    var buttonSpan = document.createElement("span");
                    buttonSpan.className = "input-group-btn";
                    
                    var removeBtn = document.createElement("button");
                    removeBtn.type = "button";
                    removeBtn.className = "btn btn-danger";
                    removeBtn.innerHTML = "Rimuovi";
                    removeBtn.onclick = function() { removeFontField(this, fontCount); };
                    
                    buttonSpan.appendChild(removeBtn);
                    inputGroup.appendChild(display);
                    inputGroup.appendChild(hiddenInput);
                    inputGroup.appendChild(buttonSpan);
                    div.appendChild(inputGroup);
                    container.appendChild(div);
                    
                    // Pulisci il campo file
                    fileInput.value = "";
                }
            }
            
            function removeFontField(btn, index) {
                if (confirm("Sei sicuro di voler rimuovere questo font?")) {
                    var div = btn.parentNode.parentNode.parentNode;
                    div.parentNode.removeChild(div);
                    
                    // Se non ci sono più font, rimuovi il titolo
                    var container = document.getElementById("fonts_list");
                    if (container.children.length <= 1) {
                        container.innerHTML = "";
                    }
                }
            }
        </script>
        <style>
            .input-group {
                width: 100%;
            }
            
            .fixed-width-sm {
                width: 100px !important;
            }
            
            .btn-primary {
                background-color: #2fb5d2;
                border-color: #2fb5d2;
            }
            
            input[type="color"] {
                cursor: pointer;
            }
            
            .form-control[readonly] {
                background-color: #eee;
            }
            
            .row {
                margin-left: 0;
                margin-right: 0;
            }
            
            .col-md-2, .col-md-4, .col-md-5, .col-md-10 {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            #box_colors_container .form-group {
                margin-bottom: 0;
            }
            
            #box_colors_list .form-group {
                margin-bottom: 10px;
            }
        </style>';
        
        return $js;
    }

// AGGIUNGERE QUESTI METODI ALLA FINE DELLA CLASSE (prima dell'ultima parentesi graffa):

/**
 * Hook per il salvataggio nel carrello
 */
public function hookActionCartSave($params)
{
    // Gestione personalizzazioni al salvataggio carrello
    if (isset($params['cart'])) {
        ArtPuzzleLogger::log('Cart save event triggered', 'INFO', ['cart_id' => $params['cart']->id]);
    }
}

/**
 * Hook per conferma ordine
 */
public function hookDisplayOrderConfirmation($params)
{
    if (isset($params['order'])) {
        $order = $params['order'];
        
        // Invia email finali se ci sono puzzle personalizzati
        $this->sendFinalOrderNotifications($order);
        
        return $this->display(__FILE__, 'views/templates/hook/displayOrderConfirmation.tpl');
    }
    
    return '';
}

/**
 * Invia notifiche finali per ordine confermato
 */
private function sendFinalOrderNotifications($order)
{
    // Logica per email finali con PDF allegati
    ArtPuzzleLogger::log('Sending final order notifications for order: ' . $order->id, 'INFO');
    
    // Qui implementerai l'invio delle email finali
}

}