<?php
/**
 * FotoArt Puzzle Module
 *
 * @author    FotoArt
 * @copyright 2024
 * @license   Proprietary
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$fapDependencies = [
    'FAPLogger' => '/classes/FAPLogger.php',
    'FAPPuzzleRepository' => '/classes/FAPPuzzleRepository.php',
    'FAPConfiguration' => '/classes/FAPConfiguration.php',
    'FAPPathBuilder' => '/classes/FAPPathBuilder.php',
    'FAPCleanupService' => '/classes/FAPCleanupService.php',
    'FAPFormatManager' => '/classes/FAPFormatManager.php',
    'FAPImageProcessor' => '/classes/FAPImageProcessor.php',
    'FAPQualityService' => '/classes/FAPQualityService.php',
    'FAPImageAnalysis' => '/classes/FAPImageAnalysis.php',
    'FAPBoxRenderer' => '/classes/FAPBoxRenderer.php',
    'FAPPdfGenerator' => '/classes/FAPPdfGenerator.php',
    'FAPCustomizationService' => '/classes/FAPCustomizationService.php',
    'FAPAssetGenerationService' => '/classes/FAPAssetGenerationService.php',
    'FAPSessionService' => '/classes/FAPSessionService.php',
    'FAPFontManager' => '/classes/FAPFontManager.php',
    'FAPSecurityTokenService' => '/classes/FAPSecurityTokenService.php',
    'FAPPathValidator' => '/classes/FAPPathValidator.php',
];

foreach ($fapDependencies as $className => $path) {
    if (!class_exists($className)) {
        require_once __DIR__ . $path;
    }
}

class FotoArtPuzzle extends Module
{
    public const MODULE_NAME = 'fotoartpuzzle';

    /**
     * @var array
     */
    private $hooks = [
        'displayHeader',
        'displayProductAdditionalInfo',
        'displayAdminOrderMain',
        'actionFrontControllerSetMedia',
        'actionObjectOrderAddAfter',
        'actionCartSave',
        'displayShoppingCartFooter',
        'displayBackOfficeHeader',
        'displayProductButtons',
        'displayAdminProductsMainStepLeftColumnMiddle',  // Compatibile con PS 1.7.6.9
        'displayAdminProductsOptionsStepTop',  // Hook aggiuntivo per migliore visibilità
        'actionCronJob',
    ];

    /**
     * FotoArtPuzzle constructor.
     */
    public function __construct()
    {
        $this->name = self::MODULE_NAME;
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'FotoArt';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.6.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('FotoArt Custom Puzzle');
        $this->description = $this->l('Allow customers to create a custom jigsaw puzzle with their own image.');
        $this->confirmUninstall = $this->l('Do you really want to uninstall the FotoArt Puzzle module?');
    }

    /**
     * Install module
     *
     * @return bool
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        foreach ($this->hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        if (!FAPConfiguration::installDefaults()) {
            return false;
        }

        if (!$this->ensureSecuritySecret()) {
            return false;
        }

        if (!FAPPathBuilder::ensureFilesystem()) {
            return false;
        }

        if (!$this->installDatabase()) {
            return false;
        }

        return $this->installTab();
    }

    /**
     * Uninstall module
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall()
            && FAPConfiguration::removeDefaults()
            && $this->uninstallDatabase()
            && $this->uninstallTab();
    }

    /**
     * Module configuration page
     *
     * @return string
     */
    public function getContent()
    {
        if ($this->isAjaxConfigurationRequest()) {
            $this->handleAjaxRequest();
        }

        $output = '';

        if (Tools::isSubmit('submit_fap_config')) {
            $output .= $this->processConfigurationSave();
        }

        try {
            $form = $this->renderForm();
            return $output . $form;
        } catch (Exception $e) {
            FAPLogger::create()->error('Error rendering configuration form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $output . $this->displayError(
                $this->l('An error occurred while loading the configuration page: ') . $e->getMessage()
            );
        }
    }

    /**
     * Build settings form
     *
     * @return string
     */
    protected function renderForm()
    {
        $values = $this->getConfigFormValues();
        $productIds = FAPConfiguration::getEnabledProductIds();
        $products = $this->getPuzzleProductsForDisplay($productIds);
        $colorCombinations = $this->decodeColorCombinations($values[FAPConfiguration::BOX_COLOR_COMBINATIONS]);
        $fonts = $this->decodeFonts($values[FAPConfiguration::CUSTOM_FONTS]);
        $legacyMappings = $this->decodeLegacyMappings($values[FAPConfiguration::PUZZLE_LEGACY_MAP]);

        $configKeys = [
            'max_upload_size' => FAPConfiguration::MAX_UPLOAD_SIZE,
            'min_width' => FAPConfiguration::MIN_WIDTH,
            'min_height' => FAPConfiguration::MIN_HEIGHT,
            'allowed_extensions' => FAPConfiguration::ALLOWED_EXTENSIONS,
            'upload_folder' => FAPConfiguration::UPLOAD_FOLDER,
            'box_default_text' => FAPConfiguration::BOX_DEFAULT_TEXT,
            'box_max_chars' => FAPConfiguration::BOX_MAX_CHARS,
            'box_color' => FAPConfiguration::BOX_COLOR,
            'box_text_color' => FAPConfiguration::BOX_TEXT_COLOR,
            'box_color_combinations' => FAPConfiguration::BOX_COLOR_COMBINATIONS,
            'custom_fonts' => FAPConfiguration::CUSTOM_FONTS,
            'email_preview_user' => FAPConfiguration::EMAIL_PREVIEW_USER,
            'email_preview_admin' => FAPConfiguration::EMAIL_PREVIEW_ADMIN,
            'email_admin_recipients' => FAPConfiguration::EMAIL_ADMIN_RECIPIENTS,
            'enable_pdf_user' => FAPConfiguration::ENABLE_PDF_USER,
            'enable_pdf_admin' => FAPConfiguration::ENABLE_PDF_ADMIN,
            'enable_orientation' => FAPConfiguration::ENABLE_ORIENTATION,
            'enable_interactive_crop' => FAPConfiguration::ENABLE_INTERACTIVE_CROP,
            'puzzle_products' => FAPConfiguration::PUZZLE_PRODUCTS,
            'legacy_map' => FAPConfiguration::PUZZLE_LEGACY_MAP,
        ];

        $this->context->smarty->assign([
            'form_action' => $this->getAdminFormAction(),
            'module_name' => $this->name,
            'tab_module' => $this->tab,
            'token' => Tools::getAdminTokenLite('AdminModules'),
            'ajax_url' => $this->getAjaxUrl(),
            'module_dir' => $this->_path,
            'config_keys' => $configKeys,
            'config' => [
                'max_upload_size' => (int) $values[FAPConfiguration::MAX_UPLOAD_SIZE],
                'min_width' => (int) $values[FAPConfiguration::MIN_WIDTH],
                'min_height' => (int) $values[FAPConfiguration::MIN_HEIGHT],
                'allowed_extensions' => (string) $values[FAPConfiguration::ALLOWED_EXTENSIONS],
                'upload_folder' => (string) $values[FAPConfiguration::UPLOAD_FOLDER],
                'box_default_text' => (string) $values[FAPConfiguration::BOX_DEFAULT_TEXT],
                'box_max_chars' => (int) $values[FAPConfiguration::BOX_MAX_CHARS],
                'box_color' => (string) $values[FAPConfiguration::BOX_COLOR],
                'box_text_color' => (string) $values[FAPConfiguration::BOX_TEXT_COLOR],
                'email_preview_user' => (bool) $values[FAPConfiguration::EMAIL_PREVIEW_USER],
                'email_preview_admin' => (bool) $values[FAPConfiguration::EMAIL_PREVIEW_ADMIN],
                'email_admin_recipients' => (string) $values[FAPConfiguration::EMAIL_ADMIN_RECIPIENTS],
                'enable_pdf_user' => (bool) $values[FAPConfiguration::ENABLE_PDF_USER],
                'enable_pdf_admin' => (bool) $values[FAPConfiguration::ENABLE_PDF_ADMIN],
                'enable_orientation' => (bool) $values[FAPConfiguration::ENABLE_ORIENTATION],
                'enable_interactive_crop' => (bool) $values[FAPConfiguration::ENABLE_INTERACTIVE_CROP],
                'puzzle_products_raw' => (string) $values[FAPConfiguration::PUZZLE_PRODUCTS],
                'custom_fonts_json' => json_encode(array_map(function ($font) {
                    return is_array($font) && isset($font['name']) ? $font['name'] : $font;
                }, $fonts)),
                'color_combinations_json' => json_encode($colorCombinations),
                'legacy_map_json' => json_encode($legacyMappings),
            ],
            'color_combinations' => $colorCombinations,
            'fonts' => $fonts,
            'puzzle_products' => $products,
            'legacy_mappings' => $legacyMappings,
            'translations' => $this->getAdminTranslations(),
        ]);

        return $this->fetch('module:' . self::MODULE_NAME . '/views/templates/admin/configure.tpl');
    }

    private function isAjaxConfigurationRequest()
    {
        if (Tools::getValue('configure') !== $this->name) {
            return false;
        }

        if (!Tools::getIsset('ajax') || !Tools::getValue('ajax')) {
            return false;
        }

        return (bool) Tools::getValue('fap_action');
    }

    private function handleAjaxRequest()
    {
        header('Content-Type: application/json');

        $action = Tools::getValue('fap_action');
        $response = ['success' => false];

        try {
            $this->assertValidAdminToken();

            switch ($action) {
                case 'addProduct':
                    $response = $this->ajaxAddProduct();
                    break;
                case 'removeProduct':
                    $response = $this->ajaxRemoveProduct();
                    break;
                case 'addColorCombination':
                    $response = $this->ajaxAddColorCombination();
                    break;
                case 'removeColorCombination':
                    $response = $this->ajaxRemoveColorCombination();
                    break;
                case 'uploadFont':
                    $response = $this->ajaxUploadFont();
                    break;
                case 'removeFont':
                    $response = $this->ajaxRemoveFont();
                    break;
                default:
                    throw new Exception($this->l('Unknown action.'));
            }
        } catch (Exception $exception) {
            FAPLogger::create()->error('AJAX request failed', [
                'action' => $action,
                'error' => $exception->getMessage()
            ]);
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
        }

        echo json_encode($response);
        exit;
    }

    private function assertValidAdminToken()
    {
        $token = (string) Tools::getValue('token');
        $expected = Tools::getAdminTokenLite('AdminModules');

        if (!$token || $token !== $expected) {
            throw new Exception($this->l('Invalid security token.'));
        }
    }

    private function processConfigurationSave()
    {
        $fields = [
            FAPConfiguration::MAX_UPLOAD_SIZE,
            FAPConfiguration::MIN_WIDTH,
            FAPConfiguration::MIN_HEIGHT,
            FAPConfiguration::ALLOWED_EXTENSIONS,
            FAPConfiguration::UPLOAD_FOLDER,
            FAPConfiguration::BOX_DEFAULT_TEXT,
            FAPConfiguration::BOX_MAX_CHARS,
            FAPConfiguration::EMAIL_ADMIN_RECIPIENTS,
        ];

        foreach ($fields as $field) {
            $value = Tools::getValue($field);

            if ($field === FAPConfiguration::UPLOAD_FOLDER) {
                $value = $this->sanitizeUploadFolder((string) $value);
            }

            Configuration::updateValue($field, $value);
        }

        $booleanFields = [
            FAPConfiguration::EMAIL_PREVIEW_USER,
            FAPConfiguration::EMAIL_PREVIEW_ADMIN,
            FAPConfiguration::ENABLE_PDF_USER,
            FAPConfiguration::ENABLE_PDF_ADMIN,
            FAPConfiguration::ENABLE_ORIENTATION,
            FAPConfiguration::ENABLE_INTERACTIVE_CROP,
        ];

        foreach ($booleanFields as $field) {
            $value = Tools::getValue($field, '0');
            Configuration::updateValue($field, (string) $value === '1' ? 1 : 0);
        }

        $boxColor = $this->sanitizeColor(Tools::getValue(FAPConfiguration::BOX_COLOR));
        $textColor = $this->sanitizeColor(Tools::getValue(FAPConfiguration::BOX_TEXT_COLOR));
        Configuration::updateValue(FAPConfiguration::BOX_COLOR, $boxColor ?: '#FFFFFF');
        Configuration::updateValue(FAPConfiguration::BOX_TEXT_COLOR, $textColor ?: '#000000');

        $colorCombinations = $this->decodeColorCombinations(Tools::getValue(FAPConfiguration::BOX_COLOR_COMBINATIONS, '[]'));
        Configuration::updateValue(FAPConfiguration::BOX_COLOR_COMBINATIONS, json_encode($colorCombinations));

        $fonts = $this->decodeFonts(Tools::getValue(FAPConfiguration::CUSTOM_FONTS, '[]'));
        Configuration::updateValue(FAPConfiguration::CUSTOM_FONTS, $this->encodeFontsForStorage($fonts));

        $products = $this->sanitizeProductCsv(Tools::getValue(FAPConfiguration::PUZZLE_PRODUCTS, ''));
        $this->persistPuzzleProducts($products);

        $legacyMappings = $this->sanitizeLegacyMappings(Tools::getValue(FAPConfiguration::PUZZLE_LEGACY_MAP, '[]'));
        Configuration::updateValue(FAPConfiguration::PUZZLE_LEGACY_MAP, json_encode($legacyMappings));

        return $this->displayConfirmation($this->l('Settings updated successfully.'));
    }

    private function sanitizeUploadFolder($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '/upload/';
        }

        $value = str_replace(chr(92), '/', $value);
        if ($value[0] !== '/') {
            $value = '/' . $value;
        }

        return rtrim($value, '/') . '/';
    }

    private function getAdminFormAction()
    {
        $base = $this->context->link->getAdminLink('AdminModules');

        return $base
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;
    }

    private function getAjaxUrl()
    {
        return $this->getAdminFormAction() . '&ajax=1';
    }

    private function decodeColorCombinations($value)
    {
        $decoded = json_decode((string) $value, true);
        if (!is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $combination) {
            if (!is_array($combination)) {
                continue;
            }

            $box = $this->sanitizeColor($combination['box'] ?? '');
            $text = $this->sanitizeColor($combination['text'] ?? '');
            if ($box && $text) {
                $result[] = ['box' => $box, 'text' => $text];
            }
        }

        return $result;
    }

    private function decodeLegacyMappings($value)
    {
        return $this->sanitizeLegacyMappings($value);
    }

    private function sanitizeLegacyMappings($value)
    {
        $decoded = is_array($value) ? $value : json_decode((string) $value, true);
        if (!is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $idProduct = isset($entry['id_product']) ? (int) $entry['id_product'] : 0;
            $legacyCode = isset($entry['legacy_code']) ? trim((string) $entry['legacy_code']) : '';

            if ($idProduct <= 0 || $legacyCode === '') {
                continue;
            }

            $idProductAttribute = isset($entry['id_product_attribute']) ? (int) $entry['id_product_attribute'] : 0;
            $pieces = isset($entry['pieces']) ? (int) $entry['pieces'] : null;
            $width = isset($entry['width_mm']) ? (int) $entry['width_mm'] : null;
            $height = isset($entry['height_mm']) ? (int) $entry['height_mm'] : null;
            $price = null;
            if (isset($entry['price']) && $entry['price'] !== '') {
                $price = (float) str_replace(',', '.', (string) $entry['price']);
                if ($price < 0) {
                    $price = null;
                }
            }

            $result[] = [
                'id_product' => $idProduct,
                'id_product_attribute' => $idProductAttribute > 0 ? $idProductAttribute : 0,
                'legacy_code' => Tools::substr($legacyCode, 0, 128),
                'pieces' => ($pieces !== null && $pieces > 0) ? $pieces : null,
                'width_mm' => ($width !== null && $width > 0) ? $width : null,
                'height_mm' => ($height !== null && $height > 0) ? $height : null,
                'price' => $price !== null ? number_format($price, 2, '.', '') : null,
                'available' => !empty($entry['available']),
            ];
        }

        return array_values($result);
    }

    private function decodeFonts($value)
    {
        $decoded = is_array($value) ? $value : json_decode((string) $value, true);
        if (!is_array($decoded)) {
            $decoded = [];
        }

        $manager = new FAPFontManager();
        $available = $manager->getAvailableFonts();
        if (!$available) {
            return [];
        }

        $byName = [];
        foreach ($available as $font) {
            $font['url'] = _MODULE_DIR_ . self::MODULE_NAME . '/fonts/' . rawurlencode($font['filename']);
            $byName[Tools::strtolower($font['name'])] = $font;
        }

        $fonts = [];
        foreach ($decoded as $font) {
            $name = null;
            if (is_array($font) && isset($font['name'])) {
                $name = Tools::strtolower($font['name']);
            } elseif (is_string($font)) {
                $name = Tools::strtolower($font);
            }

            if ($name && isset($byName[$name])) {
                $fonts[$name] = $byName[$name];
            }
        }

        if (!$fonts) {
            $fonts = $byName;
        }

        return array_values($fonts);
    }

    private function encodeFontsForStorage(array $fonts)
    {
        $names = [];
        foreach ($fonts as $font) {
            if (is_array($font) && isset($font['name'])) {
                $names[] = (string) $font['name'];
            } elseif (is_string($font)) {
                $names[] = $font;
            }
        }

        $names = array_values(array_unique(array_filter($names)));

        return json_encode($names);
    }

    private function sanitizeColor($color)
    {
        $color = strtoupper(trim((string) $color));
        if (!preg_match('/^#[0-9A-F]{6}$/', $color)) {
            return '';
        }

        return $color;
    }

    private function sanitizeProductCsv($value)
    {
        if (!is_string($value)) {
            return [];
        }

        $ids = array_map('intval', array_map('trim', explode(',', $value)));

        return array_values(array_filter($ids));
    }

    private function persistPuzzleProducts(array $ids)
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $ids = array_filter($ids);

        Configuration::updateValue(FAPConfiguration::PUZZLE_PRODUCTS, $ids ? implode(',', $ids) : '');
    }

    private function getPuzzleProductsForDisplay(array $ids)
    {
        if (!$ids) {
            return [];
        }

        $idLang = (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;

        $sql = 'SELECT p.id_product, pl.name '
            . 'FROM ' . _DB_PREFIX_ . 'product p '
            . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl '
            . 'ON (p.id_product = pl.id_product '
            . 'AND pl.id_lang = ' . $idLang
            . ' AND pl.id_shop = ' . $idShop . ') '
            . 'WHERE p.id_product IN (' . implode(',', array_map('intval', $ids)) . ')';

        $rows = Db::getInstance()->executeS($sql) ?: [];
        $names = [];
        foreach ($rows as $row) {
            $names[(int) $row['id_product']] = $row['name'];
        }

        $result = [];
        foreach ($ids as $id) {
            $result[] = [
                'id_product' => (int) $id,
                'name' => $names[(int) $id] ?? $this->l('Product not found'),
            ];
        }

        return $result;
    }

    private function getAdminTranslations()
    {
        return [
            'products_heading' => $this->l('PUZZLE PRODUCTS'),
            'product_id_label' => $this->l('Product ID'),
            'add_product' => $this->l('Add'),
            'configuration_upload' => $this->l('UPLOAD CONFIGURATION'),
            'max_upload' => $this->l('Maximum upload size (MB)'),
            'allowed_extensions' => $this->l('Allowed image formats'),
            'min_width' => $this->l('Minimum width (px)'),
            'min_height' => $this->l('Minimum height (px)'),
            'upload_folder' => $this->l('Image upload folder'),
            'box_heading' => $this->l('BOX CUSTOMIZATION'),
            'box_default_text' => $this->l('Default box text'),
            'box_default_text_desc' => $this->l('Enter the default text for the box'),
            'box_max_chars' => $this->l('Maximum text length'),
            'box_max_chars_desc' => $this->l('Set the maximum text length (e.g. 30)'),
            'box_color' => $this->l('Box color'),
            'box_text_color' => $this->l('Text color'),
            'add_combination' => $this->l('ADD'),
            'color_combinations' => $this->l('Preset color combinations'),
            'fonts_heading' => $this->l('CUSTOM FONTS'),
            'upload_font' => $this->l('Upload TTF Font'),
            'add_font' => $this->l('Add Font'),
            'functionality_heading' => $this->l('FEATURES'),
            'enable_orientation' => $this->l('Enable orientation'),
            'enable_crop' => $this->l('Enable interactive crop'),
            'email_heading' => $this->l('EMAIL NOTIFICATIONS AND PDF'),
            'email_user' => $this->l('Send preview to user via email'),
            'email_admin' => $this->l('Send preview to admin via email'),
            'email_admin_recipients' => $this->l('Administrator email'),
            'enable_pdf_user' => $this->l('Enable PDF for user'),
            'enable_pdf_admin' => $this->l('Enable PDF for admin'),
            'legacy_heading' => $this->l('LEGACY PUZZLE MAPPING'),
            'legacy_product_label' => $this->l('Product ID'),
            'legacy_attribute_label' => $this->l('Combination ID (optional)'),
            'legacy_code_label' => $this->l('Legacy code'),
            'legacy_pieces_label' => $this->l('Pieces'),
            'legacy_width_label' => $this->l('Width (mm)'),
            'legacy_height_label' => $this->l('Height (mm)'),
            'legacy_price_label' => $this->l('Price'),
            'legacy_available_label' => $this->l('Available'),
            'legacy_add' => $this->l('Add mapping'),
            'legacy_empty' => $this->l('No mappings configured yet.'),
            'legacy_validation_error' => $this->l('Please fill product ID and legacy code. Price must be positive.'),
            'yes' => $this->l('Yes'),
            'no' => $this->l('No'),
            'save' => $this->l('Save'),
            'remove' => $this->l('Remove'),
            'error' => $this->l('An error occurred.'),
            'success' => $this->l('Operation completed.'),
        ];
    }

    private function ajaxAddProduct()
    {
        $productId = (int) Tools::getValue('productId');
        if ($productId <= 0) {
            throw new Exception($this->l('Invalid product ID.'));
        }

        $exists = (bool) Db::getInstance()->getValue(
            'SELECT 1 FROM ' . _DB_PREFIX_ . 'product WHERE id_product = ' . (int) $productId
        );

        if (!$exists) {
            throw new Exception($this->l('Product not found.'));
        }

        $ids = FAPConfiguration::getEnabledProductIds();
        if (!in_array($productId, $ids, true)) {
            $ids[] = $productId;
            $this->persistPuzzleProducts($ids);
        }

        return [
            'success' => true,
            'products' => $this->getPuzzleProductsForDisplay($ids),
            'csv' => Configuration::get(FAPConfiguration::PUZZLE_PRODUCTS),
        ];
    }

    private function ajaxRemoveProduct()
    {
        $productId = (int) Tools::getValue('productId');
        if ($productId <= 0) {
            throw new Exception($this->l('Invalid product ID.'));
        }

        $ids = array_filter(FAPConfiguration::getEnabledProductIds(), static function ($id) use ($productId) {
            return (int) $id !== $productId;
        });

        $this->persistPuzzleProducts($ids);

        return [
            'success' => true,
            'products' => $this->getPuzzleProductsForDisplay($ids),
            'csv' => Configuration::get(FAPConfiguration::PUZZLE_PRODUCTS),
        ];
    }

    private function ajaxAddColorCombination()
    {
        $box = $this->sanitizeColor(Tools::getValue('boxColor'));
        $text = $this->sanitizeColor(Tools::getValue('textColor'));

        if (!$box || !$text) {
            throw new Exception($this->l('Invalid colors.'));
        }

        $combinations = $this->decodeColorCombinations(Configuration::get(FAPConfiguration::BOX_COLOR_COMBINATIONS));
        $combinations[] = ['box' => $box, 'text' => $text];
        Configuration::updateValue(FAPConfiguration::BOX_COLOR_COMBINATIONS, json_encode($combinations));

        return [
            'success' => true,
            'combinations' => $combinations,
        ];
    }

    private function ajaxRemoveColorCombination()
    {
        $index = (int) Tools::getValue('index');
        $combinations = $this->decodeColorCombinations(Configuration::get(FAPConfiguration::BOX_COLOR_COMBINATIONS));

        if (!isset($combinations[$index])) {
            throw new Exception($this->l('Combination not found.'));
        }

        unset($combinations[$index]);
        $combinations = array_values($combinations);
        Configuration::updateValue(FAPConfiguration::BOX_COLOR_COMBINATIONS, json_encode($combinations));

        return [
            'success' => true,
            'combinations' => $combinations,
        ];
    }

    private function ajaxUploadFont()
    {
        if (empty($_FILES['font']) || !is_uploaded_file($_FILES['font']['tmp_name'])) {
            throw new Exception($this->l('No file uploaded.'));
        }

        $file = $_FILES['font'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception($this->l('Error uploading font.'));
        }

        $manager = new FAPFontManager();
        $fontInfo = $manager->handleUpload($file);

        $fonts = $this->decodeFonts(Configuration::get(FAPConfiguration::CUSTOM_FONTS));
        $fonts[] = $fontInfo;
        $encoded = $this->encodeFontsForStorage($fonts);
        Configuration::updateValue(FAPConfiguration::CUSTOM_FONTS, $encoded);

        return [
            'success' => true,
            'fonts' => $this->decodeFonts($encoded),
        ];
    }

    private function ajaxRemoveFont()
    {
        $fontName = trim((string) Tools::getValue('fontName'));
        if ($fontName === '') {
            throw new Exception($this->l('Invalid font name.'));
        }

        $fonts = $this->decodeFonts(Configuration::get(FAPConfiguration::CUSTOM_FONTS));
        $remaining = [];
        foreach ($fonts as $font) {
            if (Tools::strtolower($font['filename']) === Tools::strtolower($fontName)
                || Tools::strtolower($font['name']) === Tools::strtolower(pathinfo($fontName, PATHINFO_FILENAME))) {
                $fontPath = _PS_MODULE_DIR_ . self::MODULE_NAME . '/fonts/' . $font['filename'];
                if (is_file($fontPath)) {
                    @unlink($fontPath);
                }
                continue;
            }

            $remaining[] = $font;
        }

        Configuration::updateValue(FAPConfiguration::CUSTOM_FONTS, $this->encodeFontsForStorage($remaining));

        return [
            'success' => true,
            'fonts' => $remaining,
        ];
    }

    /**
     * Retrieve current configuration values
     *
     * @return array
     */
    protected function getConfigFormValues()
    {
        return [
            FAPConfiguration::MAX_UPLOAD_SIZE => Configuration::get(FAPConfiguration::MAX_UPLOAD_SIZE),
            FAPConfiguration::MIN_WIDTH => Configuration::get(FAPConfiguration::MIN_WIDTH),
            FAPConfiguration::MIN_HEIGHT => Configuration::get(FAPConfiguration::MIN_HEIGHT),
            FAPConfiguration::ALLOWED_EXTENSIONS => Configuration::get(FAPConfiguration::ALLOWED_EXTENSIONS),
            FAPConfiguration::UPLOAD_FOLDER => Configuration::get(FAPConfiguration::UPLOAD_FOLDER),
            FAPConfiguration::BOX_DEFAULT_TEXT => Configuration::get(FAPConfiguration::BOX_DEFAULT_TEXT),
            FAPConfiguration::BOX_MAX_CHARS => Configuration::get(FAPConfiguration::BOX_MAX_CHARS),
            FAPConfiguration::BOX_COLOR => Configuration::get(FAPConfiguration::BOX_COLOR),
            FAPConfiguration::BOX_TEXT_COLOR => Configuration::get(FAPConfiguration::BOX_TEXT_COLOR),
            FAPConfiguration::BOX_COLOR_COMBINATIONS => Configuration::get(FAPConfiguration::BOX_COLOR_COMBINATIONS) ?: '[]',
            FAPConfiguration::CUSTOM_FONTS => Configuration::get(FAPConfiguration::CUSTOM_FONTS) ?: '[]',
            FAPConfiguration::EMAIL_PREVIEW_USER => (int) Configuration::get(FAPConfiguration::EMAIL_PREVIEW_USER),
            FAPConfiguration::EMAIL_PREVIEW_ADMIN => (int) Configuration::get(FAPConfiguration::EMAIL_PREVIEW_ADMIN),
            FAPConfiguration::EMAIL_ADMIN_RECIPIENTS => Configuration::get(FAPConfiguration::EMAIL_ADMIN_RECIPIENTS),
            FAPConfiguration::ENABLE_PDF_USER => (int) Configuration::get(FAPConfiguration::ENABLE_PDF_USER),
            FAPConfiguration::ENABLE_PDF_ADMIN => (int) Configuration::get(FAPConfiguration::ENABLE_PDF_ADMIN),
            FAPConfiguration::ENABLE_ORIENTATION => (int) Configuration::get(FAPConfiguration::ENABLE_ORIENTATION),
            FAPConfiguration::ENABLE_INTERACTIVE_CROP => (int) Configuration::get(FAPConfiguration::ENABLE_INTERACTIVE_CROP),
            FAPConfiguration::PUZZLE_PRODUCTS => (string) Configuration::get(FAPConfiguration::PUZZLE_PRODUCTS),
            FAPConfiguration::PUZZLE_LEGACY_MAP => (string) Configuration::get(FAPConfiguration::PUZZLE_LEGACY_MAP),
        ];
    }

    /**
     * Front office header hook
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->registerJavascript(
            'modules-' . $this->name . '-wizard',
            $this->_path . 'views/js/puzzle.js',
            ['position' => 'bottom', 'priority' => 150]
        );
        $this->context->controller->registerStylesheet(
            'modules-' . $this->name . '-wizard',
            $this->_path . 'views/css/puzzle.css',
            ['media' => 'all', 'priority' => 150]
        );
    }

    /**
     * Product page hook - displayProductAdditionalInfo
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        // Non renderizzare in questo hook per evitare duplicati
        // Il wizard verrà mostrato solo tramite displayProductButtons
        return '';
    }

    /**
     * Product page hook - displayProductButtons (prioritario)
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductButtons($params)
    {
        $product = $params['product'] ?? null;
        if (!$product || !isset($product['id_product'])) {
            return '';
        }

        if (!FAPConfiguration::isProductEnabled($product['id_product'])) {
            return '';
        }

        $config = FAPConfiguration::getFrontConfig();

        $this->context->smarty->assign([
            'upload_url' => $this->context->link->getModuleLink(self::MODULE_NAME, 'upload'),
            'preview_url' => $this->context->link->getModuleLink(self::MODULE_NAME, 'preview'),
            'summary_url' => $this->context->link->getModuleLink(self::MODULE_NAME, 'summary'),
            'ajax_url' => $this->context->link->getModuleLink(self::MODULE_NAME, 'ajax'),
            'config' => json_encode($config),
            'id_product' => (int) $product['id_product'],
            'token_upload' => $this->getFrontToken('upload'),
            'token_preview' => $this->getFrontToken('preview'),
            'token_summary' => $this->getFrontToken('summary'),
            'token_ajax' => $this->getFrontToken('ajax'),
            'module' => $this,
        ]);

        return $this->fetch('module:' . self::MODULE_NAME . '/views/templates/hook/product_buttons.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->hookDisplayHeader();
    }

    public function hookDisplayShoppingCartFooter($params)
    {
        $cart = isset($params['cart']) ? $params['cart'] : $this->context->cart;
        if (!$cart || !$cart->id) {
            return '';
        }

        $customizations = FAPCustomizationService::getCartCustomizations($cart->id);
        $this->context->smarty->assign([
            'customizations' => $customizations,
            'module' => $this,
        ]);

        return $this->fetch('module:' . self::MODULE_NAME . '/views/templates/hook/cart_footer.tpl');
    }

    public function hookDisplayAdminOrderMain($params)
    {
        if (empty($params['order'])) {
            return '';
        }
        $order = $params['order'];
        $this->context->smarty->assign([
            'customizations' => FAPCustomizationService::getOrderCustomizations((int) $order->id),
            'module' => $this,
        ]);

        return $this->fetch('module:' . self::MODULE_NAME . '/views/templates/hook/admin_order.tpl');
    }

    public function hookActionObjectOrderAddAfter($params)
    {
        if (empty($params['object']) || !$params['object'] instanceof Order) {
            return;
        }

        $order = $params['object'];
        FAPCustomizationService::finalizeOrderCustomizations($order);

        $customizations = FAPCustomizationService::getOrderCustomizations((int) $order->id);
        if (!$customizations) {
            return;
        }

        $this->ensureProductionRecord($order);

        $pdfArtifacts = [];
        $customerAttachments = [];
        $adminAttachments = [];

        $generateUserPdf = (bool) Configuration::get(FAPConfiguration::ENABLE_PDF_USER);
        $generateAdminPdf = (bool) Configuration::get(FAPConfiguration::ENABLE_PDF_ADMIN);

        if ($generateUserPdf || $generateAdminPdf) {
            try {
                $pdfGenerator = new FAPPdfGenerator($this);

                if ($generateUserPdf) {
                    $userPdf = $pdfGenerator->generate($order, $customizations, [
                        'scope' => 'user',
                        'id_lang' => (int) $order->id_lang,
                    ]);
                    if ($userPdf && !empty($userPdf['path']) && file_exists($userPdf['path'])) {
                        $pdfArtifacts['user'] = $userPdf;

                        if (Configuration::get(FAPConfiguration::EMAIL_PREVIEW_USER)) {
                            $content = Tools::file_get_contents($userPdf['path']);
                            if ($content !== false) {
                                $customerAttachments[] = [
                                    'content' => $content,
                                    'name' => $userPdf['filename'],
                                    'mime' => 'application/pdf',
                                ];
                            }
                        }
                    }
                }

                if ($generateAdminPdf) {
                    $adminPdf = $pdfGenerator->generate($order, $customizations, [
                        'scope' => 'admin',
                        'id_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
                    ]);
                    if ($adminPdf && !empty($adminPdf['path']) && file_exists($adminPdf['path'])) {
                        $pdfArtifacts['admin'] = $adminPdf;

                        if (Configuration::get(FAPConfiguration::EMAIL_PREVIEW_ADMIN)) {
                            $content = Tools::file_get_contents($adminPdf['path']);
                            if ($content !== false) {
                                $adminAttachments[] = [
                                    'content' => $content,
                                    'name' => $adminPdf['filename'],
                                    'mime' => 'application/pdf',
                                ];
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                FAPLogger::create()->error('Failed to generate FotoArt PDF summary', [
                    'order_id' => (int) $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!empty($pdfArtifacts)) {
            foreach ($customizations as $index => $customization) {
                $metadata = isset($customization['metadata']) && is_array($customization['metadata'])
                    ? $customization['metadata']
                    : [];

                if (!isset($metadata['asset_map']) || !is_array($metadata['asset_map'])) {
                    $metadata['asset_map'] = [];
                }

                foreach ($pdfArtifacts as $scope => $pdfInfo) {
                    if (empty($pdfInfo['path'])) {
                        continue;
                    }

                    $metadata['asset_map']['pdf_' . $scope] = [
                        'path' => $pdfInfo['path'],
                        'filename' => $pdfInfo['filename'],
                        'scope' => $scope,
                    ];
                }

                FAPCustomizationService::saveMetadata($customization['id_customization'], $metadata);
                $customizations[$index]['metadata'] = $metadata;
            }
        }

        if (Configuration::get(FAPConfiguration::EMAIL_PREVIEW_ADMIN)
            && empty($adminAttachments)
            && isset($pdfArtifacts['user'])
            && !empty($pdfArtifacts['user']['path'])
            && file_exists($pdfArtifacts['user']['path'])) {
            $content = Tools::file_get_contents($pdfArtifacts['user']['path']);
            if ($content !== false) {
                $adminAttachments[] = [
                    'content' => $content,
                    'name' => $pdfArtifacts['user']['filename'],
                    'mime' => 'application/pdf',
                ];
            }
        }

        if (Configuration::get(FAPConfiguration::EMAIL_PREVIEW_USER)) {
            $this->sendCustomerEmail($order, $customizations, $customerAttachments);
        }

        if (Configuration::get(FAPConfiguration::EMAIL_PREVIEW_ADMIN)) {
            $this->sendAdminEmail($order, $customizations, $adminAttachments);
        }
    }

    public function hookActionCartSave($params)
    {
        FAPCleanupService::fromModule($this)->cleanupTemporary();
    }

    public function hookActionCronJob($params)
    {
        FAPCleanupService::fromModule($this)->runHousekeeping();
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');

        // Per la pagina di configurazione del modulo
        if (Tools::getValue('configure') === $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addCSS($this->_path . 'views/css/admin-config.css');
            
            // Registra il JavaScript con jQuery come dipendenza
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . 'views/js/admin-config.js');
        }
        
        // Per la dashboard di produzione
        if ($controller === 'AdminFotoArtPuzzle') {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        }
    }


    /**
     * Hook for the left column of the product page (PS 1.7.6+)
     * Compatibile con PrestaShop 1.7.6.9
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $idProduct = (int) Tools::getValue('id_product');
        if (!$idProduct) {
            return '';
        }

        if (!FAPConfiguration::isProductEnabled($idProduct)) {
            return $this->displayInfo(
                $this->l('This product is not configured for custom puzzles.') . '<br>' .
                $this->l('Go to the module configuration to enable it.')
            );
        }

        $config = FAPConfiguration::getFrontConfig();

        $this->context->smarty->assign([
            'id_product' => $idProduct,
            'config' => $config,
            'formats' => $config['formats'] ?? [],
            'module_name' => $this->name,
            'module_display_name' => $this->displayName,
            'configure_url' => $this->context->link->getAdminLink('AdminModules') .
                              '&configure=' . $this->name,
        ]);

        return $this->fetch('module:' . self::MODULE_NAME . '/views/templates/admin/product_extra.tpl');
    }
    /**
     * Hook per visualizzare nella sezione Options della pagina prodotto (PS 1.7.6+)
     * Compatibile con PrestaShop 1.7.6.9
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAdminProductsOptionsStepTop($params)
    {
        $idProduct = (int) Tools::getValue('id_product');
        if (!$idProduct) {
            return '';
        }

        if (!FAPConfiguration::isProductEnabled($idProduct)) {
            return $this->displayInfo(
                $this->l('This product is not configured for custom puzzles.') . '<br>' .
                $this->l('Go to the module configuration to enable it.')
            );
        }

        $config = FAPConfiguration::getFrontConfig();

        $this->context->smarty->assign([
            'id_product' => $idProduct,
            'config' => $config,
            'formats' => $config['formats'] ?? [],
            'module_name' => $this->name,
            'module_display_name' => $this->displayName,
            'configure_url' => $this->context->link->getAdminLink('AdminModules') .
                              '&configure=' . $this->name,
        ]);

        return $this->fetch('module:' . self::MODULE_NAME . '/views/templates/admin/product_extra.tpl');
    }

    /**
     * Generate a token for front controllers
     *
     * @param string $scope
     *
     * @return string
     */
    public function getFrontToken($scope)
    {
        try {
            $service = $this->buildFrontTokenService();
            if (!$service) {
                return '';
            }

            $issued = $service->issue([
                'scope' => 'front:' . (string) $scope,
                'cart_id' => isset($this->context->cart->id) ? (int) $this->context->cart->id : 0,
            ], $this->getFrontTokenTtl());

            return $issued['token'];
        } catch (Exception $exception) {
            FAPLogger::create()->warning('Unable to issue front token', [
                'scope' => $scope,
                'error' => $exception->getMessage(),
            ]);

            return '';
        }
    }

    /**
     * Validate a previously issued front token.
     *
     * @param string $token
     * @param string $scope
     *
     * @return bool
     */
    public function validateFrontToken($token, $scope)
    {
        try {
            $service = $this->buildFrontTokenService();
            if (!$service) {
                return false;
            }

            $service->validate((string) $token, [
                'scope' => 'front:' . (string) $scope,
            ]);

            return true;
        } catch (Exception $exception) {
            FAPLogger::create()->warning('Front token validation failed', [
                'scope' => $scope,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Determine TTL for front tokens in seconds.
     *
     * @return int
     */
    private function getFrontTokenTtl()
    {
        return 900;
    }

    /**
     * Get customer secure key for token generation
     *
     * @return string
     */
    private function getCustomerSecureKey()
    {
        if ($this->context->customer->isLogged()) {
            return $this->context->customer->secure_key;
        }

        if (isset($this->context->cookie->id_guest) && $this->context->cookie->id_guest) {
            return 'guest_' . $this->context->cookie->id_guest;
        }

        if (isset($this->context->cart->id) && $this->context->cart->id) {
            return 'cart_' . $this->context->cart->id;
        }

        return session_id() ?: 'anonymous';
    }

    /**
     * Retrieve or generate a persistent session secret used for front tokens
     *
     * @return string
     */
    private function getFrontSessionSecret()
    {
        if (!isset($this->context) || !isset($this->context->cookie)) {
            return '';
        }

        if (empty($this->context->cookie->fap_session_secret)) {
            $this->context->cookie->fap_session_secret = Tools::passwdGen(64);
        }

        return (string) $this->context->cookie->fap_session_secret;
    }

    /**
     * Build token service for the current front context.
     *
     * @return FAPSecurityTokenService|null
     */
    private function buildFrontTokenService()
    {
        $sessionSecret = $this->getFrontSessionSecret();
        if ($sessionSecret === '') {
            return null;
        }

        $customerKey = $this->getCustomerSecureKey();
        $secret = hash('sha256', $this->getSecuritySecret() . '|' . $sessionSecret . '|' . $customerKey . '|' . _COOKIE_KEY_);

        return new FAPSecurityTokenService($secret);
    }

    /**
     * Build download token service based on the module secret.
     *
     * @return FAPSecurityTokenService
     */
    private function buildDownloadTokenService()
    {
        $secret = hash('sha256', $this->getSecuritySecret() . '|' . _COOKIE_KEY_);

        return new FAPSecurityTokenService($secret);
    }

    /**
     * Retrieve module scoped security secret.
     *
     * @return string
     */
    private function getSecuritySecret()
    {
        $secret = (string) Configuration::get(FAPConfiguration::SECURITY_SECRET);
        if ($secret === '') {
            $secret = Tools::passwdGen(64);
            if (!Configuration::updateValue(FAPConfiguration::SECURITY_SECRET, $secret)) {
                throw new RuntimeException('Unable to persist module security secret');
            }
        }

        return $secret;
    }

    /**
     * Ensure the security secret is available in configuration storage.
     *
     * @return bool
     */
    private function ensureSecuritySecret()
    {
        try {
            $this->getSecuritySecret();

            return true;
        } catch (Exception $exception) {
            FAPLogger::create()->error('Unable to ensure FotoArt security secret', [
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Create signed download URL
     *
     * @param string $path
     * @param string $scope
     *
     * @return string
     */
    public function getDownloadLink($path, $scope = 'admin', array $options = [])
    {
        try {
            $canonicalPath = FAPPathValidator::assertReadablePath($path);
        } catch (Exception $exception) {
            FAPLogger::create()->warning('Refused to build download link for invalid path', [
                'path' => $path,
                'error' => $exception->getMessage(),
            ]);

            return '';
        }

        $scope = $this->normaliseDownloadScope($scope);
        $ttl = isset($options['ttl']) ? (int) $options['ttl'] : 3600;
        if ($ttl <= 0) {
            $ttl = 3600;
        }

        $idOrder = isset($options['id_order']) ? (int) $options['id_order'] : 0;
        $disposition = isset($options['disposition']) && $options['disposition'] === 'inline' ? 'inline' : 'attachment';

        $employeeId = 0;
        if ($scope === 'admin') {
            $employeeId = $this->getAuthenticatedEmployeeId();
            if ($employeeId <= 0) {
                FAPLogger::create()->warning('Refused to build admin download link without authenticated employee', [
                    'path' => $path,
                ]);

                return '';
            }
        }

        try {
            $service = $this->buildDownloadTokenService();
            $claims = [
                'scope' => 'download:' . $scope,
                'path_hash' => hash('sha256', $canonicalPath),
                'id_order' => $idOrder,
                'disposition' => $disposition,
            ];

            if ($employeeId > 0) {
                $claims['employee_id'] = $employeeId;
            }

            $issued = $service->issue($claims, $ttl);
        } catch (Exception $exception) {
            FAPLogger::create()->error('Unable to issue download token', [
                'path' => $path,
                'scope' => $scope,
                'error' => $exception->getMessage(),
            ]);

            return '';
        }

        $params = [
            'token' => $issued['token'],
            'path' => $canonicalPath,
            'scope' => $scope,
            'disposition' => $disposition,
            'expires' => isset($issued['payload']['exp']) ? (int) $issued['payload']['exp'] : (time() + $ttl),
        ];

        if ($idOrder) {
            $params['id_order'] = $idOrder;
        }

        unset($options['ttl']);

        return $this->context->link->getModuleLink(self::MODULE_NAME, 'download', array_merge($params, $options));
    }

    /**
     * Validate download token
     *
     * @param string $token
     * @param string $path
     * @param string $scope
     * @param int|null $expires
     * @param int|null $idOrder
     * @param string|null $disposition
     *
     * @return bool
     */
    public function validateDownloadToken($token, $path, $scope, $expires = null, $idOrder = null, $disposition = null)
    {
        try {
            $canonicalPath = FAPPathValidator::assertReadablePath($path);
        } catch (Exception $exception) {
            FAPLogger::create()->warning('Download path validation failed', [
                'path' => $path,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }

        $scope = $this->normaliseDownloadScope($scope);

        try {
            $service = $this->buildDownloadTokenService();
            $payload = $service->validate((string) $token, [
                'scope' => 'download:' . $scope,
            ]);
        } catch (Exception $exception) {
            FAPLogger::create()->warning('Download token validation failed', [
                'scope' => $scope,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }

        $claims = isset($payload['claims']) && is_array($payload['claims']) ? $payload['claims'] : [];
        $expectedHash = hash('sha256', $canonicalPath);

        if (!isset($claims['path_hash']) || !hash_equals($expectedHash, (string) $claims['path_hash'])) {
            return false;
        }

        $payloadDisposition = isset($claims['disposition']) ? (string) $claims['disposition'] : 'attachment';
        if ($disposition !== null && $payloadDisposition !== (string) $disposition) {
            return false;
        }

        $payloadOrderId = isset($claims['id_order']) ? (int) $claims['id_order'] : 0;
        if ($payloadOrderId && $idOrder && (int) $idOrder !== $payloadOrderId) {
            return false;
        }

        $orderToCheck = $payloadOrderId ?: (int) $idOrder;
        if (!$this->isAuthorizedForDownload($scope, $orderToCheck, $claims)) {
            return false;
        }

        if ($expires !== null && (int) $payload['exp'] !== (int) $expires) {
            return false;
        }

        return true;
    }

    /**
     * Normalize download scope input.
     *
     * @param string $scope
     *
     * @return string
     */
    private function normaliseDownloadScope($scope)
    {
        return $scope === 'admin' ? 'admin' : 'front';
    }

    /**
     * Validate scope specific permissions for download.
     *
     * @param string $scope
     * @param int $idOrder
     *
     * @return bool
     */
    private function isAuthorizedForDownload($scope, $idOrder, array $claims = [])
    {
        if ($scope === 'admin') {
            return $this->isCurrentEmployeeAuthorizedForAdminDownload($claims);
        }

        if ($scope === 'front' && $idOrder) {
            $order = new Order((int) $idOrder);
            if (!Validate::isLoadedObject($order)) {
                return false;
            }

            return $this->context->customer->isLogged() && (int) $order->id_customer === (int) $this->context->customer->id;
        }

        return true;
    }

    /**
     * Determine whether current employee session matches admin download claims.
     *
     * @param array $claims
     *
     * @return bool
     */
    private function isCurrentEmployeeAuthorizedForAdminDownload(array $claims)
    {
        if (!isset($claims['employee_id'])) {
            return false;
        }

        $expectedEmployeeId = (int) $claims['employee_id'];
        if ($expectedEmployeeId <= 0) {
            return false;
        }

        $currentEmployeeId = $this->getCurrentEmployeeIdForAdminDownload();
        if ($currentEmployeeId <= 0) {
            return false;
        }

        return $currentEmployeeId === $expectedEmployeeId;
    }

    /**
     * Resolve the identifier for the employee currently authenticated in back office.
     *
     * @return int
     */
    private function getCurrentEmployeeIdForAdminDownload()
    {
        $employeeId = $this->getAuthenticatedEmployeeId();
        if ($employeeId > 0) {
            return $employeeId;
        }

        return $this->getEmployeeIdFromAdminCookie();
    }

    /**
     * Attempt to read the employee identifier from the back-office cookie.
     *
     * @return int
     */
    private function getEmployeeIdFromAdminCookie()
    {
        if (!isset($this->context) || !isset($this->context->cookie)) {
            return 0;
        }

        $cookie = $this->context->cookie;

        $employeeId = $this->extractEmployeeIdFromCookiePayload($cookie);
        if ($employeeId > 0) {
            return $employeeId;
        }

        foreach ($this->getCandidateAdminCookieNames() as $cookieName) {
            $cookieInstance = $this->loadAdminCookieInstance($cookieName);
            if ($cookieInstance === null) {
                continue;
            }

            $employeeId = $this->extractEmployeeIdFromCookiePayload($cookieInstance);
            if ($employeeId > 0) {
                return $employeeId;
            }
        }

        if (!empty($_COOKIE)) {
            $employeeId = $this->extractEmployeeIdFromCookiePayload($_COOKIE);
            if ($employeeId > 0) {
                return $employeeId;
            }
        }

        return 0;
    }

    /**
     * Retrieve the identifier for the authenticated employee in back office context.
     *
     * @return int
     */
    private function getAuthenticatedEmployeeId()
    {
        if (!isset($this->context) || !isset($this->context->employee) || !is_object($this->context->employee)) {
            return 0;
        }

        $employee = $this->context->employee;

        if (!method_exists($employee, 'isLoggedBack') || !$employee->isLoggedBack()) {
            return 0;
        }

        return (int) $employee->id;
    }

    /**
     * Attempt to instantiate the admin cookie for the provided name.
     *
     * @param string $cookieName
     *
     * @return Cookie|null
     */
    private function loadAdminCookieInstance($cookieName)
    {
        if (!class_exists('Cookie') || !method_exists('Cookie', '__construct')) {
            return null;
        }

        try {
            return new Cookie($cookieName, '/', null, false, null, false);
        } catch (Exception $exception) {
            return null;
        } catch (Error $error) {
            return null;
        }
    }

    /**
     * Build a list of candidate back-office cookie names.
     *
     * @return array
     */
    private function getCandidateAdminCookieNames()
    {
        $names = [];

        if (defined('_PS_ADMIN_COOKIE_NAME_')) {
            $names[] = (string) _PS_ADMIN_COOKIE_NAME_;
        }

        if (defined('_COOKIE_ADMIN_')) {
            $names[] = (string) _COOKIE_ADMIN_;
        }

        if (defined('_PS_COOKIE_ADMIN_')) {
            $names[] = (string) _PS_COOKIE_ADMIN_;
        }

        if (isset($this->context, $this->context->shop) && isset($this->context->shop->id)) {
            $names[] = 'psAdmin' . (int) $this->context->shop->id;
        }

        $names[] = 'psAdmin';

        if (!empty($_COOKIE)) {
            foreach (array_keys($_COOKIE) as $cookieName) {
                if (strpos($cookieName, 'psAdmin') === 0) {
                    $names[] = (string) $cookieName;
                }
            }
        }

        return array_values(array_unique(array_filter($names)));
    }

    /**
     * Extract the employee identifier from the given cookie payload.
     *
     * @param mixed $cookie
     *
     * @return int
     */
    private function extractEmployeeIdFromCookiePayload($cookie)
    {
        if (is_object($cookie) && isset($cookie->id_employee)) {
            return (int) $cookie->id_employee;
        }

        if (is_array($cookie) && isset($cookie['id_employee'])) {
            return (int) $cookie['id_employee'];
        }

        return 0;
    }

    /**
     * Determine if the provided path is within allowed directories.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isAllowedDownloadPath($path)
    {
        return FAPPathValidator::isAllowed($path);
    }

    /**
     * Create required database tables
     *
     * @return bool
     */
    private function installDatabase()
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;

        $queries = [
            'CREATE TABLE IF NOT EXISTS `' . $prefix . 'fap_production_order` (
                `id_fap_production` INT(11) NOT NULL AUTO_INCREMENT,
                `id_order` INT(11) NOT NULL,
                `status` VARCHAR(32) NOT NULL,
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_fap_production`),
                UNIQUE KEY `id_order` (`id_order`)
            ) ENGINE=' . $engine . ' DEFAULT CHARSET=utf8mb4',
            'CREATE TABLE IF NOT EXISTS `' . $prefix . 'fap_puzzle_format` (
                `id_fap_puzzle_format` INT(11) NOT NULL AUTO_INCREMENT,
                `reference` VARCHAR(64) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `pieces` INT(11) NOT NULL DEFAULT 0,
                `width_cm` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `height_cm` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `shape` VARCHAR(64) DEFAULT NULL,
                `price` DECIMAL(20,6) DEFAULT NULL,
                `image` VARCHAR(255) DEFAULT NULL,
                `payload` LONGTEXT DEFAULT NULL,
                `position` INT(11) NOT NULL DEFAULT 0,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_fap_puzzle_format`),
                UNIQUE KEY `reference` (`reference`)
            ) ENGINE=' . $engine . ' DEFAULT CHARSET=utf8mb4',
            'CREATE TABLE IF NOT EXISTS `' . $prefix . 'fap_puzzle_box` (
                `id_fap_puzzle_box` INT(11) NOT NULL AUTO_INCREMENT,
                `reference` VARCHAR(64) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `template` VARCHAR(255) DEFAULT NULL,
                `preview` VARCHAR(255) DEFAULT NULL,
                `color` VARCHAR(32) DEFAULT NULL,
                `text_color` VARCHAR(32) DEFAULT NULL,
                `payload` LONGTEXT DEFAULT NULL,
                `position` INT(11) NOT NULL DEFAULT 0,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_fap_puzzle_box`),
                UNIQUE KEY `reference` (`reference`)
            ) ENGINE=' . $engine . ' DEFAULT CHARSET=utf8mb4',
        ];

        foreach ($queries as $sql) {
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }

        return $this->seedReferenceData();
    }

    /**
     * Drop module tables on uninstall
     *
     * @return bool
     */
    private function uninstallDatabase()
    {
        $queries = [
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'fap_puzzle_box`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'fap_puzzle_format`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'fap_production_order`',
        ];

        foreach ($queries as $sql) {
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Seed default puzzle formats and boxes for first installation.
     *
     * @return bool
     */
    private function seedReferenceData()
    {
        $db = Db::getInstance();
        $now = date('Y-m-d H:i:s');

        $formatTable = _DB_PREFIX_ . 'fap_puzzle_format';
        if (!(int) $db->getValue('SELECT COUNT(*) FROM `' . $formatTable . '`')) {
            $defaultFormats = [
                [
                    'reference' => 'PUZ-500',
                    'name' => 'Puzzle 500 pezzi',
                    'pieces' => 500,
                    'width_cm' => 49.0,
                    'height_cm' => 36.0,
                    'shape' => null,
                    'price' => null,
                    'image' => null,
                    'payload' => [],
                ],
                [
                    'reference' => 'PUZ-1000',
                    'name' => 'Puzzle 1000 pezzi',
                    'pieces' => 1000,
                    'width_cm' => 68.0,
                    'height_cm' => 48.0,
                    'shape' => null,
                    'price' => null,
                    'image' => null,
                    'payload' => [],
                ],
            ];

            foreach ($defaultFormats as $position => $format) {
                $db->insert('fap_puzzle_format', [
                    'reference' => pSQL($format['reference']),
                    'name' => pSQL($format['name']),
                    'pieces' => (int) $format['pieces'],
                    'width_cm' => sprintf('%.2f', (float) $format['width_cm']),
                    'height_cm' => sprintf('%.2f', (float) $format['height_cm']),
                    'shape' => $format['shape'] ? pSQL($format['shape']) : null,
                    'price' => $format['price'] !== null ? (float) $format['price'] : null,
                    'image' => $format['image'] ? pSQL($format['image']) : null,
                    'payload' => !empty($format['payload']) ? pSQL(json_encode($format['payload'])) : null,
                    'position' => (int) $position,
                    'active' => 1,
                    'date_add' => pSQL($now),
                    'date_upd' => pSQL($now),
                ]);
            }
        }

        $boxTable = _DB_PREFIX_ . 'fap_puzzle_box';
        if (!(int) $db->getValue('SELECT COUNT(*) FROM `' . $boxTable . '`')) {
            $defaultBoxes = [
                [
                    'reference' => 'BOX-CLASSIC',
                    'name' => 'Scatola classica',
                    'template' => null,
                    'preview' => null,
                    'color' => '#FFFFFF',
                    'text_color' => '#000000',
                    'payload' => [],
                ],
            ];

            foreach ($defaultBoxes as $position => $box) {
                $db->insert('fap_puzzle_box', [
                    'reference' => pSQL($box['reference']),
                    'name' => pSQL($box['name']),
                    'template' => $box['template'] ? pSQL($box['template']) : null,
                    'preview' => $box['preview'] ? pSQL($box['preview']) : null,
                    'color' => $box['color'] ? pSQL($box['color']) : null,
                    'text_color' => $box['text_color'] ? pSQL($box['text_color']) : null,
                    'payload' => !empty($box['payload']) ? pSQL(json_encode($box['payload'])) : null,
                    'position' => (int) $position,
                    'active' => 1,
                    'date_add' => pSQL($now),
                    'date_upd' => pSQL($now),
                ]);
            }
        }

        return true;
    }

    /**
     * Ensure a production record exists for the order
     *
     * @param Order $order
     */
    private function ensureProductionRecord(Order $order)
    {
        $now = date('Y-m-d H:i:s');

        Db::getInstance()->insert(
            'fap_production_order',
            [
                'id_order' => (int) $order->id,
                'status' => pSQL('pending'),
                'date_add' => pSQL($now),
                'date_upd' => pSQL($now),
            ],
            false,
            true,
            Db::REPLACE
        );
    }

    /**
     * Send confirmation email to customer
     *
     * @param Order $order
     * @param array $customizations
     */
    private function sendCustomerEmail(Order $order, array $customizations, array $attachments = [])
    {
        if (empty($order->id_customer)) {
            return;
        }

        $customer = new Customer((int) $order->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            return;
        }

        $idLang = (int) $order->id_lang ?: (int) Configuration::get('PS_LANG_DEFAULT');

        $templateVars = array_merge(
            [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{order_reference}' => $order->reference,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            ],
            [
                'customizations' => $this->mapCustomizationsForEmail($customizations, [
                    'include_links' => true,
                    'scope' => 'front',
                ]),
            ]
        );

        Mail::Send(
            $idLang,
            'fap_customer_notification',
            $this->l('Your custom FotoArt puzzle order is confirmed'),
            $templateVars,
            $customer->email,
            trim($customer->firstname . ' ' . $customer->lastname),
            null,
            null,
            $attachments,
            null,
            _PS_MODULE_DIR_ . self::MODULE_NAME . '/mails/'
        );
    }

    /**
     * Send notification email to administrators
     *
     * @param Order $order
     * @param array $customizations
     */
    private function sendAdminEmail(Order $order, array $customizations, array $attachments = [])
    {
        $recipients = $this->parseAdminRecipients(Configuration::get(FAPConfiguration::EMAIL_ADMIN_RECIPIENTS));
        if (empty($recipients)) {
            return;
        }

        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $customer = new Customer((int) $order->id_customer);
        $templateVars = array_merge(
            [
                '{order_reference}' => $order->reference,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{customer_name}' => $customer && Validate::isLoadedObject($customer)
                    ? trim($customer->firstname . ' ' . $customer->lastname)
                    : $this->l('Guest'),
                'order_link' => $this->context->link->getAdminLink('AdminOrders', true, [], [
                    'vieworder' => 1,
                    'id_order' => (int) $order->id,
                ]),
            ],
            [
                'customizations' => $this->mapCustomizationsForEmail($customizations, [
                    'include_links' => true,
                    'scope' => 'admin',
                ]),
            ]
        );

        foreach ($recipients as $email => $name) {
            Mail::Send(
                $idLang,
                'fap_admin_notification',
                $this->l('New custom FotoArt puzzle order'),
                $templateVars,
                $email,
                $name,
                null,
                null,
                $attachments,
                null,
                _PS_MODULE_DIR_ . self::MODULE_NAME . '/mails/'
            );
        }
    }

    /**
     * Expose mapped customization metadata for downstream consumers (PDF, API, etc.).
     *
     * @param array $customizations
     * @param array $options
     *
     * @return array
     */
    public function getCustomizationDisplayData(array $customizations, array $options = [])
    {
        return $this->mapCustomizationsForEmail($customizations, $options);
    }

    /**
     * Normalize customization data for email templates
     *
     * @param array $customizations
     * @param array $options
     *
     * @return array
     */
    private function mapCustomizationsForEmail(array $customizations, array $options = [])
    {
        $includeLinks = !empty($options['include_links']);
        $scope = isset($options['scope']) ? (string) $options['scope'] : 'front';

        $mapped = [];
        foreach ($customizations as $customization) {
            $metadata = is_array($customization['metadata']) ? $customization['metadata'] : [];
            $displayMetadata = [];
            if (!empty($metadata['format'])) {
                $displayMetadata[$this->l('Format')] = $metadata['format'];
            }
            if (!empty($metadata['box_name'])) {
                $displayMetadata[$this->l('Box')] = $metadata['box_name'];
            }
            if (!empty($metadata['color'])) {
                $displayMetadata[$this->l('Color')] = $metadata['color'];
            }
            if (!empty($metadata['font'])) {
                $displayMetadata[$this->l('Font')] = $metadata['font'];
            }
            if (!empty($metadata['pieces'])) {
                $displayMetadata[$this->l('Pieces')] = (int) $metadata['pieces'];
            }

            $qualityLabel = $this->resolveQualityLabel($metadata);
            if ($qualityLabel) {
                $displayMetadata[$this->l('Quality')] = $qualityLabel;
            }

            if (!empty($metadata['orientation'])) {
                $displayMetadata[$this->l('Orientation')] = $metadata['orientation'] === 'portrait'
                    ? $this->l('Portrait')
                    : $this->l('Landscape');
            }

            $previewPath = !empty($metadata['preview_path']) ? $metadata['preview_path'] : null;
            $pdfKey = $scope === 'admin' ? 'pdf_admin' : 'pdf_user';
            $pdfAttached = !empty($metadata['asset_map'][$pdfKey]['path']);
            $mapped[] = [
                'id_customization' => $customization['id_customization'],
                'text' => $customization['text'],
                'metadata' => $displayMetadata,
                'has_preview' => (bool) $previewPath,
                'preview_link' => ($includeLinks && $previewPath)
                    ? $this->getDownloadLink($previewPath, $scope, ['disposition' => 'inline'])
                    : null,
                'image_link' => ($includeLinks && !empty($customization['file']))
                    ? $this->getDownloadLink($customization['file'], $scope, ['disposition' => 'inline'])
                    : null,
                'preview_attached' => false,
                'pdf_attached' => $pdfAttached,
            ];
        }

        return $mapped;
    }

    /**
     * Resolve a translated quality label from customization metadata.
     *
     * @param array $metadata
     *
     * @return string|null
     */
    private function resolveQualityLabel(array $metadata)
    {
        if (!empty($metadata['print_info']) && is_array($metadata['print_info'])) {
            if (!empty($metadata['print_info']['quality_label'])) {
                return $this->translateQualityKey($metadata['print_info']['quality_label']);
            }
            if (isset($metadata['print_info']['quality'])) {
                return $this->translateQualityKey($this->qualityKeyFromScore((int) $metadata['print_info']['quality']));
            }
        }

        if (array_key_exists('quality', $metadata)) {
            return $this->translateQualityKey($this->qualityKeyFromScore((int) $metadata['quality']));
        }

        return null;
    }

    /**
     * Map a score to a quality keyword.
     *
     * @param int $score
     *
     * @return string
     */
    private function qualityKeyFromScore($score)
    {
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

    /**
     * Translate a quality keyword into the current language.
     *
     * @param string|null $key
     *
     * @return string|null
     */
    private function translateQualityKey($key)
    {
        if (!$key) {
            return null;
        }

        switch ((string) $key) {
            case 'excellent':
                return $this->l('Excellent');
            case 'great':
                return $this->l('Great');
            case 'good':
                return $this->l('Good');
            case 'poor':
                return $this->l('Fair');
            case 'insufficient':
            default:
                return $this->l('Insufficient');
        }
    }

    /**
     * Parse administrator email recipients
     *
     * @param string $value
     *
     * @return array
     */
    private function parseAdminRecipients($value)
    {
        $recipients = [];
        $parts = preg_split('/[\s,;]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            $email = trim($part);
            if (!Validate::isEmail($email)) {
                continue;
            }
            $recipients[$email] = $email;
        }

        return $recipients;
    }

    /**
     * Install back office tab
     *
     * @return bool
     */
    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminFotoArtPuzzle');
        if ($tabId) {
            return true;
        }

        $tab = new Tab();
        $tab->class_name = 'AdminFotoArtPuzzle';
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentOrders');
        $tab->module = $this->name;
        $tab->active = 1;
        foreach (Language::getLanguages(false) as $language) {
            $tab->name[$language['id_lang']] = $this->l('Puzzle Production');
        }

        return (bool) $tab->add();
    }

    /**
     * Remove back office tab
     *
     * @return bool
     */
    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminFotoArtPuzzle');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return (bool) $tab->delete();
    }
}
