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

require_once __DIR__ . '/classes/FAPLogger.php';
require_once __DIR__ . '/classes/FAPPuzzleRepository.php';
require_once __DIR__ . '/classes/FAPConfiguration.php';
require_once __DIR__ . '/classes/FAPPathBuilder.php';
require_once __DIR__ . '/classes/FAPCleanupService.php';
require_once __DIR__ . '/classes/FAPFormatManager.php';
require_once __DIR__ . '/classes/FAPImageProcessor.php';
require_once __DIR__ . '/classes/FAPQualityService.php';
require_once __DIR__ . '/classes/FAPImageAnalysis.php';
require_once __DIR__ . '/classes/FAPBoxRenderer.php';
require_once __DIR__ . '/classes/FAPPdfGenerator.php';
require_once __DIR__ . '/classes/FAPCustomizationService.php';
require_once __DIR__ . '/classes/FAPAssetGenerationService.php';
require_once __DIR__ . '/classes/FAPSessionService.php';
require_once __DIR__ . '/classes/FAPFontManager.php';

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
            ],
            'color_combinations' => $colorCombinations,
            'fonts' => $fonts,
            'puzzle_products' => $products,
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
        $sessionSecret = $this->getFrontSessionSecret();
        if (!$sessionSecret) {
            return '';
        }

        $key = $this->getCustomerSecureKey();

        return hash('sha256', $scope . '|' . $key . '|' . _COOKIE_KEY_ . '|' . $sessionSecret);
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
     * Create signed download URL
     *
     * @param string $path
     * @param string $scope
     *
     * @return string
     */
    public function getDownloadLink($path, $scope = 'admin', array $options = [])
    {
        $secret = $this->getScopeSecret($scope);
        if (!$secret) {
            return '';
        }

        if ($path && file_exists($path) && !$this->isAllowedDownloadPath($path)) {
            return '';
        }

        $ttl = isset($options['ttl']) ? (int) $options['ttl'] : 3600;
        if ($ttl <= 0) {
            $ttl = 3600;
        }
        $expires = time() + $ttl;
        $idOrder = isset($options['id_order']) ? (int) $options['id_order'] : 0;

        $signature = $this->signDownloadPath($path, $scope, $expires, $idOrder, $secret);

        $params = array_merge([
            'token' => $signature,
            'path' => $path,
            'scope' => $scope,
            'expires' => $expires,
        ], $options);

        if ($idOrder) {
            $params['id_order'] = (int) $idOrder;
        }

        return $this->context->link->getModuleLink(self::MODULE_NAME, 'download', $params);
    }

    /**
     * Validate download token
     *
     * @param string $token
     * @param string $path
     * @param string $scope
     * @param int|null $expires
     * @param int|null $idOrder
     *
     * @return bool
     */
    public function validateDownloadToken($token, $path, $scope, $expires = null, $idOrder = null)
    {
        $secret = $this->getScopeSecret($scope);
        if (!$secret) {
            return false;
        }

        if ($expires && (int) $expires < time()) {
            return false;
        }

        if (!$this->isAuthorizedForDownload($scope, (int) $idOrder)) {
            return false;
        }

        $signature = $this->signDownloadPath($path, $scope, (int) $expires, (int) $idOrder, $secret);

        return hash_equals($signature, (string) $token);
    }

    /**
     * Sign download path with secret
     *
     * @param string $path
     * @param string $scope
     * @param int $expires
     * @param int $idOrder
     * @param string $token
     *
     * @return string
     */
    private function signDownloadPath($path, $scope, $expires, $idOrder, $token)
    {
        return hash('sha256', $path . '|' . $scope . '|' . (int) $expires . '|' . (int) $idOrder . '|' . $token . '|' . _COOKIE_KEY_);
    }

    /**
     * Return secret per scope
     *
     * @param string $scope
     *
     * @return string|null
     */
    private function getScopeSecret($scope)
    {
        if ($scope === 'admin') {
            return Tools::getAdminTokenLite('AdminOrders');
        }

        if ($scope === 'front') {
            return $this->getFrontToken('download');
        }

        return null;
    }

    /**
     * Validate scope specific permissions for download.
     *
     * @param string $scope
     * @param int $idOrder
     *
     * @return bool
     */
    private function isAuthorizedForDownload($scope, $idOrder)
    {
        if ($scope === 'admin') {
            return isset($this->context->employee) && $this->context->employee->id;
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
     * Determine if the provided path is within allowed directories.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isAllowedDownloadPath($path)
    {
        $realPath = realpath($path);
        if ($realPath === false) {
            return false;
        }

        $allowedRoots = array_filter([
            realpath(FAPPathBuilder::getBasePath()),
            realpath(_PS_DOWNLOAD_DIR_),
            realpath(_PS_UPLOAD_DIR_),
        ]);

        foreach ($allowedRoots as $root) {
            if (strpos($realPath, $root) === 0) {
                return true;
            }
        }

        return false;
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
