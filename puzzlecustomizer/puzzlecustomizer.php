<?php
/**
 * Puzzle Customizer Module
 *
 * @author OpenAI
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PuzzleCustomizer extends Module
{
    public function __construct()
    {
        $this->name = 'puzzlecustomizer';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'FotoArt';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        require_once __DIR__ . '/autoload.php';

        $this->displayName = $this->l('Puzzle Customizer');
        $this->description = $this->l('Consente ai clienti di personalizzare puzzle con immagini e opzioni avanzate.');
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        return $this->installTabs()
            && $this->registerHooks()
            && $this->installDatabase()
            && $this->ensureDirectories();
    }

    public function uninstall()
    {
        return $this->uninstallTabs()
            && $this->uninstallDatabase()
            && parent::uninstall();
    }

    protected function registerHooks()
    {
        $hooks = [
            'displayHeader',
            'displayFooter',
            'actionValidateOrder',
            'actionOrderStatusPostUpdate',
            'displayAdminOrder',
        ];

        foreach ($hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    protected function installTabs()
    {
        // First, create the parent tab
        if (!$this->createTab('AdminPuzzleCustomizer', 'IMPROVE', $this->l('Puzzle Customizer'))) {
            return false;
        }

        // Then create child tabs
        $tabs = [
            [
                'class_name' => 'AdminPuzzleProducts',
                'parent_class_name' => 'AdminPuzzleCustomizer',
                'name' => $this->l('Prodotti'),
            ],
            [
                'class_name' => 'AdminPuzzleOptions',
                'parent_class_name' => 'AdminPuzzleCustomizer',
                'name' => $this->l('Opzioni'),
            ],
            [
                'class_name' => 'AdminPuzzleFonts',
                'parent_class_name' => 'AdminPuzzleCustomizer',
                'name' => $this->l('Font'),
            ],
            [
                'class_name' => 'AdminPuzzleImageFormats',
                'parent_class_name' => 'AdminPuzzleCustomizer',
                'name' => $this->l('Formati immagine'),
            ],
            [
                'class_name' => 'AdminPuzzleBoxColors',
                'parent_class_name' => 'AdminPuzzleCustomizer',
                'name' => $this->l('Colori scatola'),
            ],
            [
                'class_name' => 'AdminPuzzleTextColors',
                'parent_class_name' => 'AdminPuzzleCustomizer',
                'name' => $this->l('Colori testo'),
            ],
            [
                'class_name' => 'AdminPuzzleOrders',
                'parent_class_name' => 'AdminPuzzleCustomizer',
                'name' => $this->l('Ordini'),
            ],
            [
                'class_name' => 'AdminPuzzleConfiguration',
                'parent_class_name' => 'AdminPuzzleCustomizer',
                'name' => $this->l('Configurazione'),
            ],
        ];

        foreach ($tabs as $tabData) {
            if (!$this->createTab($tabData['class_name'], $tabData['parent_class_name'], $tabData['name'])) {
                return false;
            }
        }

        return true;
    }

    protected function createTab($className, $parentClassName, $name)
    {
        $tabId = (int) Tab::getIdFromClassName($className);
        if ($tabId) {
            return true;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = [];

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }

        // Handle parent ID
        if ($parentClassName === 'IMPROVE') {
            // For PrestaShop 1.7.7+
            $tab->id_parent = (int) Tab::getIdFromClassName('IMPROVE');

            // Fallback for older versions
            if (!$tab->id_parent) {
                $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentModulesSf');
            }

            // Last fallback
            if (!$tab->id_parent) {
                $tab->id_parent = 0;
            }
        } else {
            $tab->id_parent = (int) Tab::getIdFromClassName($parentClassName);
            if (!$tab->id_parent) {
                PrestaShopLogger::addLog(
                    'Puzzle Customizer: Cannot find parent tab: ' . $parentClassName,
                    3
                );
                return false;
            }
        }

        $tab->module = $this->name;

        if (!$tab->add()) {
            PrestaShopLogger::addLog(
                'Puzzle Customizer: Failed to create tab: ' . $className . ' - ' . Db::getInstance()->getMsgError(),
                3
            );
            return false;
        }

        return true;
    }

    protected function uninstallTabs()
    {
        $classNames = [
            'AdminPuzzleConfiguration',
            'AdminPuzzleOrders',
            'AdminPuzzleTextColors',
            'AdminPuzzleBoxColors',
            'AdminPuzzleImageFormats',
            'AdminPuzzleFonts',
            'AdminPuzzleOptions',
            'AdminPuzzleProducts',
            'AdminPuzzleCustomizer',
        ];

        foreach ($classNames as $className) {
            $id = (int) Tab::getIdFromClassName($className);
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }
        }

        return true;
    }

    protected function installDatabase()
    {
        require_once __DIR__ . '/sql/install.php';

        $result = PuzzleCustomizerSqlInstall::install();

        if (!$result) {
            $this->_errors[] = $this->l('Failed to create database tables. Check PrestaShop logs for details.');

            return false;
        }

        Configuration::updateValue('PUZZLE_MAX_FILESIZE', 50);
        Configuration::updateValue('PUZZLE_DEFAULT_DPI', 300);
        Configuration::updateValue('PUZZLE_MIN_IMAGE_WIDTH', 1000);
        Configuration::updateValue('PUZZLE_MIN_IMAGE_HEIGHT', 1000);

        return true;
    }

    protected function uninstallDatabase()
    {
        include_once __DIR__ . '/sql/uninstall.php';

        return PuzzleCustomizerSqlUninstall::uninstall();
    }

    protected function ensureDirectories()
    {
        $paths = [
            _PS_MODULE_DIR_ . $this->name . '/uploads/temp/',
            _PS_MODULE_DIR_ . $this->name . '/uploads/customizations/',
            _PS_MODULE_DIR_ . $this->name . '/uploads/production/',
            _PS_MODULE_DIR_ . $this->name . '/fonts/',
        ];

        foreach ($paths as $path) {
            if (!is_dir($path) && !@mkdir($path, 0755, true)) {
                return false;
            }
        }

        return true;
    }

    public function getContent()
    {
        $link = $this->context->link->getAdminLink('AdminPuzzleConfiguration');

        Tools::redirectAdmin($link);

        return '';
    }

    public function hookDisplayHeader($params)
    {
        if ($this->isCustomizerController()) {
            $this->context->controller->registerJavascript(
                'fabricjs',
                'https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js',
                ['position' => 'bottom', 'priority' => 50, 'server' => 'remote']
            );

            $this->context->controller->registerJavascript(
                'threejs',
                'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js',
                ['position' => 'bottom', 'priority' => 51, 'server' => 'remote']
            );

            $this->context->controller->registerStylesheet(
                'module-' . $this->name . '-customizer',
                'modules/' . $this->name . '/views/css/front/customizer.css',
                ['media' => 'all', 'priority' => 100]
            );

            $this->context->controller->registerStylesheet(
                'module-' . $this->name . '-editor',
                'modules/' . $this->name . '/views/css/front/editor.css',
                ['media' => 'all', 'priority' => 100]
            );

            $this->context->controller->registerJavascript(
                'module-' . $this->name . '-validations',
                'modules/' . $this->name . '/views/js/front/validations.js',
                ['position' => 'bottom', 'priority' => 98]
            );

            $this->context->controller->registerJavascript(
                'module-' . $this->name . '-canvas',
                'modules/' . $this->name . '/views/js/front/canvas-editor.js',
                ['position' => 'bottom', 'priority' => 99]
            );

            $this->context->controller->registerJavascript(
                'module-' . $this->name . '-customizer',
                'modules/' . $this->name . '/views/js/front/customizer.js',
                ['position' => 'bottom', 'priority' => 100]
            );
        }
    }

    public function hookDisplayFooter($params)
    {
        // Hook kept for backward compatibility but no output required.
        return '';
    }

    protected function isCustomizerController()
    {
        return $this->context->controller && get_class($this->context->controller) === 'PuzzlecustomizerCustomizerModuleFrontController';
    }

    public function hookActionValidateOrder($params)
    {
        if (!isset($params['cart']) || !isset($params['order'])) {
            return;
        }

        $cart = $params['cart'];
        $order = $params['order'];

        $sql = 'UPDATE ' . _DB_PREFIX_ . 'puzzle_customization'
            . ' SET id_order = ' . (int) $order->id . ', status = "ordered", updated_at = NOW()'
            . ' WHERE id_cart = ' . (int) $cart->id;

        Db::getInstance()->execute($sql);

        $this->generateProductionFilesForOrder($order->id);
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        if (!isset($params['newOrderStatus']) || !isset($params['id_order'])) {
            return;
        }

        $idOrder = (int) $params['id_order'];
        $newStatus = $params['newOrderStatus'];

        if (in_array($newStatus->id, [3, 4])) {
            $this->generateProductionFilesForOrder($idOrder);
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        $idOrder = (int) $params['id_order'];

        $customizations = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'puzzle_customization WHERE id_order = ' . (int) $idOrder
        );

        if (empty($customizations)) {
            return '';
        }

        $this->context->smarty->assign([
            'customizations' => $customizations,
            'module_dir' => $this->getPathUri(),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/order-customizations.tpl');
    }

    protected function generateProductionFilesForOrder($idOrder)
    {
        require_once __DIR__ . '/classes/ImageProcessor.php';

        $customizations = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'puzzle_customization WHERE id_order = ' . (int) $idOrder
        );

        foreach ($customizations as $customization) {
            try {
                $processor = new ImageProcessor();
                $config = json_decode($customization['configuration'], true);

                $processor->generateProductionPackage(
                    (int) $customization['id_puzzle_customization'],
                    is_array($config) ? $config : []
                );

                Db::getInstance()->execute(
                    'UPDATE ' . _DB_PREFIX_ . 'puzzle_customization SET status = "production_ready"'
                    . ' WHERE id_puzzle_customization = ' . (int) $customization['id_puzzle_customization']
                );
            } catch (Exception $e) {
                PrestaShopLogger::addLog(
                    'Failed to generate production files for customization ' . (int) $customization['id_puzzle_customization'] . ': ' . $e->getMessage(),
                    3
                );
            }
        }
    }
}
