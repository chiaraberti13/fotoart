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
            'moduleRoutes',
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
        $tabs = [
            [
                'class_name' => 'AdminPuzzleCustomizer',
                'parent_class_name' => 'AdminParentModulesSf',
                'name' => $this->l('Puzzle Customizer'),
            ],
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

        $tab->id_parent = (int) Tab::getIdFromClassName($parentClassName);
        if (!$tab->id_parent) {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return (bool) $tab->add();
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
        include_once __DIR__ . '/sql/install.php';

        return PuzzleCustomizerSqlInstall::install();
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
                'module-' . $this->name . '-customizer',
                'modules/' . $this->name . '/views/js/front/customizer.js',
                ['position' => 'bottom', 'priority' => 100]
            );
            $this->context->controller->registerJavascript(
                'module-' . $this->name . '-canvas',
                'modules/' . $this->name . '/views/js/front/canvas-editor.js',
                ['position' => 'bottom', 'priority' => 101]
            );
        }
    }

    public function hookDisplayFooter($params)
    {
        if ($this->isCustomizerController()) {
            return $this->display(__FILE__, 'views/templates/front/preview.tpl');
        }

        return '';
    }

    public function hookModuleRoutes($params)
    {
        return [
            'module-puzzlecustomizer-customizer' => [
                'controller' => 'customizer',
                'rule' => 'puzzle/customizza',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
        ];
    }

    protected function isCustomizerController()
    {
        return $this->context->controller && get_class($this->context->controller) === 'PuzzlecustomizerCustomizerModuleFrontController';
    }
}
