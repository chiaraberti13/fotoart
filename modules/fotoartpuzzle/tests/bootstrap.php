<?php

define('_PS_VERSION_', '1.7.6.0');
define('_COOKIE_KEY_', 'test-cookie-key');
define('_PS_MODULE_DIR_', realpath(__DIR__ . '/../..') . '/');
define('_PS_DOWNLOAD_DIR_', sys_get_temp_dir() . '/fap_download/');
define('_PS_UPLOAD_DIR_', sys_get_temp_dir() . '/fap_upload/');

@mkdir(_PS_DOWNLOAD_DIR_, 0755, true);
@mkdir(_PS_UPLOAD_DIR_, 0755, true);

class Module
{
    /** @var Context */
    public $context;
    public $name = '';
    public $tab = '';
    public $version = '';
    public $author = '';
    public $need_instance = 0;
    public $ps_versions_compliancy = [];
    public $displayName = '';
    public $description = '';
    public $confirmUninstall = '';
    public $_path = '';

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function l($string)
    {
        return $string;
    }

    protected function displayError($message)
    {
        return $message;
    }

    public static function getInstanceByName($name)
    {
        return new self();
    }
}

class Context
{
    public $cookie;
    public $customer;
    public $employee;
    public $link;
    public $cart;
    public $language;
    public $shop;

    private static $instance;

    public static function getContext()
    {
        if (!self::$instance) {
            self::$instance = new self();
            self::$instance->cookie = new Cookie();
            self::$instance->customer = new Customer();
            self::$instance->employee = null;
            self::$instance->link = new Link();
            self::$instance->cart = new Cart();
            self::$instance->language = new Language();
            self::$instance->shop = new Shop();
        }

        return self::$instance;
    }
}

class Cookie
{
    public $fap_session_secret;
    public $id_employee;
    public $id_guest;
}

class Customer
{
    public $id = 0;
    public $secure_key = 'customer-secure';

    public function isLogged()
    {
        return (int) $this->id > 0;
    }
}

class Cart
{
    public $id = 1;
}

class Employee
{
    public $id = 0;
    public $loggedBack = false;

    public function isLoggedBack()
    {
        return $this->loggedBack && (int) $this->id > 0;
    }
}

class Language
{
    public $id = 1;
}

class Shop
{
    public $id = 1;
}

class Link
{
    public function getModuleLink($module, $controller, array $params = [])
    {
        return '/module/' . $module . '/' . $controller . '?' . http_build_query($params);
    }

    public function getAdminLink($controller)
    {
        return '/admin/' . $controller;
    }
}

class Tools
{
    public static function getAdminTokenLite($controller)
    {
        return 'admin-token-' . $controller;
    }

    public static function passwdGen($length = 8)
    {
        $length = max(1, (int) $length);
        return substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz0123456789', $length)), 0, $length);
    }

    public static function getValue($key, $default = null)
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $default;
    }

    public static function getIsset($key)
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public static function isSubmit($key)
    {
        return self::getIsset($key);
    }

    public static function jsonEncode($value)
    {
        return json_encode($value);
    }

    public static function str2url($string)
    {
        $string = Tools::link_rewrite($string);

        return $string;
    }

    public static function link_rewrite($string)
    {
        $string = strtolower((string) $string);
        $string = preg_replace('/[^a-z0-9]+/', '-', $string);

        return trim($string, '-');
    }

    public static function strtolower($string)
    {
        return strtolower($string);
    }

    public static function strtoupper($string)
    {
        return strtoupper($string);
    }

    public static function substr($string, $start, $length = null)
    {
        return $length === null ? substr($string, $start) : substr($string, $start, $length);
    }

    public static function strlen($string)
    {
        return strlen($string);
    }

    public static function file_get_contents($path)
    {
        return file_get_contents($path);
    }

    public static function copy($source, $destination)
    {
        return copy($source, $destination);
    }

    public static function displayDate($date)
    {
        return $date;
    }

    public static function ucfirst($string)
    {
        return ucfirst($string);
    }

    public static function getRemoteAddr()
    {
        return '127.0.0.1';
    }
}

class Order
{
    private static $orders = [];

    public $id = 0;
    public $id_customer = 0;
    public $date_add;

    public function __construct($id)
    {
        if (isset(self::$orders[$id])) {
            $this->id = (int) $id;
            $this->id_customer = (int) self::$orders[$id]['id_customer'];
            $this->date_add = date('Y-m-d H:i:s');
        }
    }

    public static function seed($id, $idCustomer)
    {
        self::$orders[(int) $id] = ['id_customer' => (int) $idCustomer];
    }
}

class Validate
{
    public static function isLoadedObject($object)
    {
        return $object instanceof Order && (int) $object->id > 0;
    }
}

class Configuration
{
    private static $storage = [];

    public static function get($key)
    {
        return array_key_exists($key, self::$storage) ? self::$storage[$key] : null;
    }

    public static function updateValue($key, $value)
    {
        self::$storage[$key] = $value;

        return true;
    }

    public static function deleteByName($key)
    {
        unset(self::$storage[$key]);

        return true;
    }
}

class Db
{
    public static function getInstance()
    {
        return new self();
    }

    public function update($table, array $data, $where)
    {
        return true;
    }

    public function delete($table, $where)
    {
        return true;
    }

    public function insert($table, array $data)
    {
        return true;
    }

    public function getValue($query)
    {
        return 0;
    }

    public function executeS($query)
    {
        return [];
    }
}

class Customization
{
    public $id;
    public $id_cart;
    public $id_product;
    public $id_product_attribute;
    public $id_shop;
    public $quantity;
    public $in_cart;

    private static $nextId = 1;

    public function __construct($id = null)
    {
        if ($id) {
            $this->id = (int) $id;
        }
    }

    public function add()
    {
        $this->id = self::$nextId++;

        return true;
    }
}

require_once _PS_MODULE_DIR_ . 'fotoartpuzzle/fotoartpuzzle.php';

Configuration::updateValue(FAPConfiguration::LOG_LEVEL, 'DEBUG');
Configuration::updateValue(FAPConfiguration::MAX_UPLOAD_SIZE, 25);
Configuration::updateValue(FAPConfiguration::MIN_WIDTH, 3000);
Configuration::updateValue(FAPConfiguration::MIN_HEIGHT, 2000);
Configuration::updateValue(FAPConfiguration::ALLOWED_EXTENSIONS, 'jpg,jpeg,png');
Configuration::updateValue(FAPConfiguration::BOX_COLOR_COMBINATIONS, json_encode([]));
Configuration::updateValue(FAPConfiguration::CUSTOM_FONTS, json_encode([]));
Configuration::updateValue(FAPConfiguration::PUZZLE_PRODUCTS, '');
Configuration::updateValue(FAPConfiguration::SECURITY_SECRET, 'tests-secret-key');

FAPPathBuilder::ensureFilesystem();
