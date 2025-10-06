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
require_once __DIR__ . '/classes/FAPConfiguration.php';
require_once __DIR__ . '/classes/FAPPathBuilder.php';
require_once __DIR__ . '/classes/FAPCleanupService.php';
require_once __DIR__ . '/classes/FAPFormatManager.php';
require_once __DIR__ . '/classes/FAPImageProcessor.php';
require_once __DIR__ . '/classes/FAPBoxRenderer.php';
require_once __DIR__ . '/classes/FAPCustomizationService.php';

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
                'custom_fonts_json' => json_encode($fonts),
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
        Configuration::updateValue(FAPConfiguration::CUSTOM_FONTS, json_encode($fonts));

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
        $decoded = json_decode((string) $value, true);
        if (!is_array($decoded)) {
            return [];
        }

        $fonts = [];
        foreach ($decoded as $font) {
            $font = trim((string) $font);
            if ($font !== '') {
                $fonts[] = $font;
            }
        }

        return array_values(array_unique($fonts));
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

        $extension = Tools::strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'ttf') {
            throw new Exception($this->l('Only TTF files are supported.'));
        }

        $fontsDir = _PS_MODULE_DIR_ . self::MODULE_NAME . '/fonts';
        if (!is_dir($fontsDir)) {
            @mkdir($fontsDir, 0750, true);
        }

        $baseName = Tools::link_rewrite(pathinfo($file['name'], PATHINFO_FILENAME));
        if ($baseName === '') {
            $baseName = 'font';
        }

        $targetName = $baseName . '-' . Tools::passwdGen(6) . '.ttf';
        $targetPath = rtrim($fontsDir, '/\\') . '/' . $targetName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception($this->l('Unable to save the uploaded font.'));
        }

        $fonts = $this->decodeFonts(Configuration::get(FAPConfiguration::CUSTOM_FONTS));
        $fonts[] = $targetName;
        $fonts = array_values(array_unique($fonts));
        Configuration::updateValue(FAPConfiguration::CUSTOM_FONTS, json_encode($fonts));

        return [
            'success' => true,
            'fonts' => $fonts,
        ];
    }

    private function ajaxRemoveFont()
    {
        $fontName = trim((string) Tools::getValue('fontName'));
        if ($fontName === '') {
            throw new Exception($this->l('Invalid font name.'));
        }

        $fonts = $this->decodeFonts(Configuration::get(FAPConfiguration::CUSTOM_FONTS));
        $fonts = array_values(array_filter($fonts, static function ($font) use ($fontName) {
            return $font !== $fontName;
        }));

        Configuration::updateValue(FAPConfiguration::CUSTOM_FONTS, json_encode($fonts));

        $fontPath = _PS_MODULE_DIR_ . self::MODULE_NAME . '/fonts/' . $fontName;
        if (is_file($fontPath)) {
            @unlink($fontPath);
        }

        return [
            'success' => true,
            'fonts' => $fonts,
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

        if (Configuration::get(FAPConfiguration::EMAIL_PREVIEW_USER)) {
            $this->sendCustomerEmail($order, $customizations);
        }

        if (Configuration::get(FAPConfiguration::EMAIL_PREVIEW_ADMIN)) {
            $this->sendAdminEmail($order, $customizations);
        }
    }

    public function hookActionCartSave($params)
    {
        FAPCleanupService::fromModule($this)->cleanupTemporary();
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
        $key = $this->getCustomerSecureKey();
        return hash('sha256', $scope . $key . _COOKIE_KEY_ . date('Ymd'));
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
        $signature = $this->signDownloadPath($path, $secret);

        $params = array_merge([
            'token' => $signature,
            'path' => $path,
            'scope' => $scope,
        ], $options);

        return $this->context->link->getModuleLink(self::MODULE_NAME, 'download', $params);
    }

    /**
     * Validate download token
     *
     * @param string $token
     * @param string $path
     * @param string $scope
     *
     * @return bool
     */
    public function validateDownloadToken($token, $path, $scope)
    {
        $secret = $this->getScopeSecret($scope);
        if (!$secret) {
            return false;
        }

        return hash_equals($this->signDownloadPath($path, $secret), (string) $token);
    }

    /**
     * Sign download path with secret
     *
     * @param string $path
     * @param string $token
     *
     * @return string
     */
    private function signDownloadPath($path, $token)
    {
        return hash('sha256', $path . '|' . $token . '|' . _COOKIE_KEY_);
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
     * Create required database tables
     *
     * @return bool
     */
    private function installDatabase()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fap_production_order` (
            `id_fap_production` INT(11) NOT NULL AUTO_INCREMENT,
            `id_order` INT(11) NOT NULL,
            `status` VARCHAR(32) NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_fap_production`),
            UNIQUE KEY `id_order` (`id_order`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4';

        return Db::getInstance()->execute($sql);
    }

    /**
     * Drop module tables on uninstall
     *
     * @return bool
     */
    private function uninstallDatabase()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'fap_production_order`';

        return Db::getInstance()->execute($sql);
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
    private function sendCustomerEmail(Order $order, array $customizations)
    {
        if (empty($order->id_customer)) {
            return;
        }

        $customer = new Customer((int) $order->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            return;
        }

        $idLang = (int) $order->id_lang ?: (int) Configuration::get('PS_LANG_DEFAULT');

        $attachments = [];

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
    private function sendAdminEmail(Order $order, array $customizations)
    {
        $recipients = $this->parseAdminRecipients(Configuration::get(FAPConfiguration::EMAIL_ADMIN_RECIPIENTS));
        if (empty($recipients)) {
            return;
        }

        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $attachments = [];

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
            if (!empty($metadata['color'])) {
                $displayMetadata[$this->l('Color')] = $metadata['color'];
            }
            if (!empty($metadata['font'])) {
                $displayMetadata[$this->l('Font')] = $metadata['font'];
            }

            $previewPath = !empty($metadata['preview_path']) ? $metadata['preview_path'] : null;
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
            ];
        }

        return $mapped;
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
