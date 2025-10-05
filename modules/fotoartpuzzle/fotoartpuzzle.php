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

        return $this->installTab();
    }

    /**
     * Uninstall module
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() && FAPConfiguration::removeDefaults() && $this->uninstallTab();
    }

    /**
     * Module configuration page
     *
     * @return string
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $formValues = $this->getConfigFormValues();
            foreach (array_keys($formValues) as $key) {
                if ($key === FAPConfiguration::ENABLED_PRODUCTS) {
                    $value = Tools::getValue($key, []);
                    $ids = array_values(array_unique(array_map('intval', (array) $value)));
                    Configuration::updateValue($key, json_encode($ids));
                    continue;
                }

                $value = Tools::getValue($key);
                Configuration::updateValue($key, is_array($value) ? json_encode($value) : $value);
            }
            $output .= $this->displayConfirmation($this->l('Settings updated successfully.'));
        }

        $this->context->smarty->assign([
            'module_dir' => $this->_path,
        ]);

        return $output . $this->renderForm();
    }

    /**
     * Build settings form
     *
     * @return string
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $values = $this->getConfigFormValues();
        $fieldsValue = $values;
        if (isset($values[FAPConfiguration::ENABLED_PRODUCTS])) {
            $fieldsValue[FAPConfiguration::ENABLED_PRODUCTS . '[]'] = $values[FAPConfiguration::ENABLED_PRODUCTS];
        }

        $helper->tpl_vars = [
            'fields_value' => $fieldsValue,
        ];

        return $helper->generateForm([
            $this->getProductFieldset(),
            $this->getUploadFieldset(),
            $this->getFormatFieldset(),
            $this->getBoxFieldset(),
            $this->getEmailFieldset(),
            $this->getPrivacyFieldset(),
            $this->getLogFieldset(),
        ]);
    }

    private function getProductFieldset()
    {
        $products = Product::getSimpleProducts($this->context->language->id) ?: [];
        $options = [];
        foreach ($products as $product) {
            $name = $product['name'];
            if (!empty($product['reference'])) {
                $name .= ' [' . $product['reference'] . ']';
            }

            $options[] = [
                'id' => (int) $product['id_product'],
                'name' => sprintf('%s (ID: %d)', $name, $product['id_product']),
            ];
        }

        return [
            'form' => [
                'legend' => ['title' => $this->l('Product availability')],
                'input' => [
                    [
                        'type' => 'select',
                        'name' => FAPConfiguration::ENABLED_PRODUCTS . '[]',
                        'label' => $this->l('Enable puzzle customization for'),
                        'multiple' => true,
                        'class' => 'chosen',
                        'options' => [
                            'query' => $options,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'desc' => $this->l('Select the products that can display the FotoArt puzzle wizard.'),
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];
    }

    /**
     * Upload settings fieldset
     *
     * @return array
     */
    private function getUploadFieldset()
    {
        return [
            'form' => [
                'legend' => ['title' => $this->l('Upload settings')],
                'input' => [
                    [
                        'type' => 'text',
                        'name' => FAPConfiguration::MAX_UPLOAD_SIZE,
                        'label' => $this->l('Maximum upload size (MB)'),
                        'class' => 'fixed-width-sm',
                    ],
                    [
                        'type' => 'text',
                        'name' => FAPConfiguration::MIN_WIDTH,
                        'label' => $this->l('Minimum width (px)'),
                        'class' => 'fixed-width-sm',
                    ],
                    [
                        'type' => 'text',
                        'name' => FAPConfiguration::MIN_HEIGHT,
                        'label' => $this->l('Minimum height (px)'),
                        'class' => 'fixed-width-sm',
                    ],
                    [
                        'type' => 'text',
                        'name' => FAPConfiguration::ALLOWED_EXTENSIONS,
                        'label' => $this->l('Allowed extensions'),
                        'desc' => $this->l('Comma separated, e.g. jpg,jpeg,png'),
                    ],
                    [
                        'type' => 'switch',
                        'name' => FAPConfiguration::FORCE_REENCODE,
                        'label' => $this->l('Force re-encoding'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'reencode_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'reencode_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'name' => FAPConfiguration::STRIP_EXIF,
                        'label' => $this->l('Strip EXIF data'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'exif_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'exif_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];
    }

    private function getFormatFieldset()
    {
        return [
            'form' => [
                'legend' => ['title' => $this->l('Puzzle formats')],
                'input' => [
                    [
                        'type' => 'textarea',
                        'name' => FAPConfiguration::FORMATS,
                        'label' => $this->l('Formats definition (JSON)'),
                        'desc' => $this->l('Example: [{"name":"500 pezzi","pieces":500,"width":5000,"height":3500,"dpi":300}]'),
                        'rows' => 6,
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];
    }

    private function getBoxFieldset()
    {
        return [
            'form' => [
                'legend' => ['title' => $this->l('Box customization')],
                'input' => [
                    [
                        'type' => 'text',
                        'name' => FAPConfiguration::BOX_MAX_CHARS,
                        'label' => $this->l('Maximum box text characters'),
                        'class' => 'fixed-width-sm',
                    ],
                    [
                        'type' => 'textarea',
                        'name' => FAPConfiguration::BOX_COLORS,
                        'label' => $this->l('Allowed colors (JSON array)'),
                        'rows' => 3,
                    ],
                    [
                        'type' => 'textarea',
                        'name' => FAPConfiguration::BOX_FONTS,
                        'label' => $this->l('Allowed fonts (JSON array)'),
                        'rows' => 3,
                    ],
                    [
                        'type' => 'switch',
                        'name' => FAPConfiguration::BOX_UPPERCASE,
                        'label' => $this->l('Uppercase text automatically'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'upper_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'upper_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];
    }

    private function getEmailFieldset()
    {
        return [
            'form' => [
                'legend' => ['title' => $this->l('Email & PDF')],
                'input' => [
                    [
                        'type' => 'switch',
                        'name' => FAPConfiguration::EMAIL_CLIENT,
                        'label' => $this->l('Send email to customer'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'email_client_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'email_client_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'name' => FAPConfiguration::EMAIL_ADMIN,
                        'label' => $this->l('Send email to admin'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'email_admin_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'email_admin_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'name' => FAPConfiguration::EMAIL_ADMIN_RECIPIENTS,
                        'label' => $this->l('Admin recipients'),
                        'desc' => $this->l('Comma separated email addresses'),
                        'rows' => 2,
                    ],
                    [
                        'type' => 'switch',
                        'name' => FAPConfiguration::EMAIL_ATTACH_PREVIEW,
                        'label' => $this->l('Attach preview image'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'preview_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'preview_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];
    }

    private function getPrivacyFieldset()
    {
        return [
            'form' => [
                'legend' => ['title' => $this->l('Privacy & cleanup')],
                'input' => [
                    [
                        'type' => 'text',
                        'name' => FAPConfiguration::TEMP_TTL_HOURS,
                        'label' => $this->l('Temporary file lifetime (hours)'),
                        'class' => 'fixed-width-sm',
                    ],
                    [
                        'type' => 'switch',
                        'name' => FAPConfiguration::ANONYMIZE_FILENAMES,
                        'label' => $this->l('Anonymize filenames'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'anon_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'anon_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];
    }

    private function getLogFieldset()
    {
        return [
            'form' => [
                'legend' => ['title' => $this->l('Logging')],
                'input' => [
                    [
                        'type' => 'select',
                        'name' => FAPConfiguration::LOG_LEVEL,
                        'label' => $this->l('Log level'),
                        'options' => [
                            'query' => [
                                ['id' => 'ERROR', 'name' => 'ERROR'],
                                ['id' => 'WARNING', 'name' => 'WARNING'],
                                ['id' => 'INFO', 'name' => 'INFO'],
                                ['id' => 'DEBUG', 'name' => 'DEBUG'],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
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
            FAPConfiguration::FORCE_REENCODE => (int) Configuration::get(FAPConfiguration::FORCE_REENCODE),
            FAPConfiguration::STRIP_EXIF => (int) Configuration::get(FAPConfiguration::STRIP_EXIF),
            FAPConfiguration::FORMATS => Configuration::get(FAPConfiguration::FORMATS),
            FAPConfiguration::BOX_MAX_CHARS => Configuration::get(FAPConfiguration::BOX_MAX_CHARS),
            FAPConfiguration::BOX_COLORS => Configuration::get(FAPConfiguration::BOX_COLORS),
            FAPConfiguration::BOX_FONTS => Configuration::get(FAPConfiguration::BOX_FONTS),
            FAPConfiguration::BOX_UPPERCASE => (int) Configuration::get(FAPConfiguration::BOX_UPPERCASE),
            FAPConfiguration::EMAIL_CLIENT => (int) Configuration::get(FAPConfiguration::EMAIL_CLIENT),
            FAPConfiguration::EMAIL_ADMIN => (int) Configuration::get(FAPConfiguration::EMAIL_ADMIN),
            FAPConfiguration::EMAIL_ADMIN_RECIPIENTS => Configuration::get(FAPConfiguration::EMAIL_ADMIN_RECIPIENTS),
            FAPConfiguration::EMAIL_ATTACH_PREVIEW => (int) Configuration::get(FAPConfiguration::EMAIL_ATTACH_PREVIEW),
            FAPConfiguration::TEMP_TTL_HOURS => Configuration::get(FAPConfiguration::TEMP_TTL_HOURS),
            FAPConfiguration::ANONYMIZE_FILENAMES => (int) Configuration::get(FAPConfiguration::ANONYMIZE_FILENAMES),
            FAPConfiguration::LOG_LEVEL => Configuration::get(FAPConfiguration::LOG_LEVEL),
            FAPConfiguration::ENABLED_PRODUCTS => json_decode((string) Configuration::get(FAPConfiguration::ENABLED_PRODUCTS), true) ?: [],
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
     * Product page hook
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->renderProductWizard($params);
    }

    public function hookDisplayProductButtons($params)
    {
        return $this->renderProductWizard($params);
    }

    private function renderProductWizard($params)
    {
        $product = $params['product'] ?? null;
        if (!$product || !FAPConfiguration::isProductEnabled($product['id_product'])) {
            return '';
        }

        $initialSummary = [];
        $cart = $this->context->cart;
        if ($cart && $cart->id) {
            $customizations = FAPCustomizationService::getCartCustomizations($cart->id);
            foreach ($customizations as $customization) {
                if ((int) $customization['id_product'] !== (int) $product['id_product']) {
                    continue;
                }

                $formatData = isset($customization['metadata']['format_data']) && is_array($customization['metadata']['format_data'])
                    ? $customization['metadata']['format_data']
                    : [];

                $initialSummary = [
                    'id_customization' => $customization['id_customization'],
                    'fileName' => $customization['metadata']['filename'] ?? '',
                    'boxText' => $customization['text'] ?? '',
                    'boxColor' => $customization['metadata']['color'] ?? '',
                    'boxFont' => $customization['metadata']['font'] ?? '',
                    'format' => $customization['metadata']['format'] ?? '',
                    'formatData' => $formatData,
                ];

                break;
            }
        }

        $this->context->smarty->assign([
            'upload_url' => $this->context->link->getModuleLink(self::MODULE_NAME, 'upload'),
            'preview_url' => $this->context->link->getModuleLink(self::MODULE_NAME, 'preview'),
            'summary_url' => $this->context->link->getModuleLink(self::MODULE_NAME, 'summary'),
            'config' => Tools::jsonEncode(FAPConfiguration::getFrontConfig()),
            'initial_summary' => $initialSummary ? Tools::jsonEncode($initialSummary) : '',
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
    }

    public function hookActionCartSave($params)
    {
        FAPCleanupService::fromModule($this)->cleanupTemporary();
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') === $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        }
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
        $customerKey = $this->context->customer->isLogged()
            ? $this->context->customer->secure_key
            : (string) ($this->context->cookie->id_guest ?: $this->context->cookie->id_cart ?: session_id());

        return Tools::getToken($scope . $customerKey);
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
            $tab->name[$language['id_lang']] = $this->l('Puzzle production');
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
