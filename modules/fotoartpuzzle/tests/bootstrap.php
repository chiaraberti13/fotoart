<?php

define('_PS_VERSION_', '1.7.8.0');
define('_COOKIE_KEY_', 'test-cookie-key');
define('_PS_MODULE_DIR_', realpath(__DIR__ . '/..') . '/');

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
}

class Context
{
    public $cookie;
    public $customer;
    public $employee;
    public $link;
    public $cart;

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
        }

        return self::$instance;
    }
}

class Cookie
{
    public $fap_session_secret;
    public $id_employee;
    public $id_guest;

    public function __construct()
    {
    }
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
    public $id = 0;
}

class Employee
{
    public $id = 0;
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
        return str_repeat('a', max(1, (int) $length));
    }

    public static function getValue($key, $default = null)
    {
        return $default;
    }
}

class Order
{
    private static $orders = [];

    public $id = 0;
    public $id_customer = 0;

    public function __construct($id)
    {
        if (isset(self::$orders[$id])) {
            $this->id = (int) $id;
            $this->id_customer = (int) self::$orders[$id];
        }
    }

    public static function seed($id, $idCustomer)
    {
        self::$orders[(int) $id] = (int) $idCustomer;
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
    public static function get($key)
    {
        return null;
    }

    public static function updateValue($key, $value)
    {
        return true;
    }
}

class FAPPathBuilder
{
    public static function getLogPath()
    {
        return sys_get_temp_dir();
    }
}

class FAPLogger
{
    public static function create()
    {
        return new self();
    }

    public function info($message, array $context = [])
    {
    }

    public function error($message, array $context = [])
    {
    }
}

class FAPPuzzleRepository
{
}

class FAPConfiguration
{
    public const MAX_UPLOAD_SIZE = 'FAP_MAX_UPLOAD_SIZE';
    public const MIN_WIDTH = 'FAP_MIN_WIDTH';
    public const MIN_HEIGHT = 'FAP_MIN_HEIGHT';
    public const ALLOWED_EXTENSIONS = 'FAP_ALLOWED_EXTENSIONS';
    public const FORMATS = 'FAP_FORMATS';
    public const UPLOAD_FOLDER = 'FAP_UPLOAD_FOLDER';
    public const BOX_MAX_CHARS = 'FAP_BOX_MAX_CHARS';
    public const BOX_DEFAULT_TEXT = 'FAP_BOX_DEFAULT_TEXT';
    public const BOX_COLOR = 'FAP_BOX_COLOR';
    public const BOX_TEXT_COLOR = 'FAP_BOX_TEXT_COLOR';
    public const BOX_COLOR_COMBINATIONS = 'FAP_BOX_COLOR_COMBINATIONS';
    public const CUSTOM_FONTS = 'FAP_CUSTOM_FONTS';
    public const EMAIL_PREVIEW_USER = 'FAP_EMAIL_PREVIEW_USER';
    public const EMAIL_PREVIEW_ADMIN = 'FAP_EMAIL_PREVIEW_ADMIN';
    public const EMAIL_ADMIN_RECIPIENTS = 'FAP_EMAIL_ADMIN_RECIPIENTS';
    public const ENABLE_PDF_USER = 'FAP_ENABLE_PDF_USER';
    public const ENABLE_PDF_ADMIN = 'FAP_ENABLE_PDF_ADMIN';
    public const ENABLE_ORIENTATION = 'FAP_ENABLE_ORIENTATION';
    public const ENABLE_INTERACTIVE_CROP = 'FAP_ENABLE_INTERACTIVE_CROP';
    public const TEMP_TTL_HOURS = 'FAP_TEMP_TTL_HOURS';
    public const ANONYMIZE_FILENAMES = 'FAP_ANONYMIZE_FILENAMES';
    public const LOG_LEVEL = 'FAP_LOG_LEVEL';
    public const PUZZLE_PRODUCTS = 'FAP_PUZZLE_PRODUCTS';
    public const PUZZLE_LEGACY_MAP = 'FAP_PUZZLE_LEGACY_MAP';

    public static function installDefaults()
    {
        return true;
    }

    public static function removeDefaults()
    {
        return true;
    }

    public static function getFrontConfig()
    {
        return [];
    }

    public static function getEnabledProductIds()
    {
        return [];
    }

    public static function isProductEnabled($idProduct)
    {
        return true;
    }
}

class FAPCleanupService
{
}

class FAPFormatManager
{
}

class FAPImageProcessor
{
}

class FAPQualityService
{
}

class FAPImageAnalysis
{
}

class FAPBoxRenderer
{
}

class FAPPdfGenerator
{
}

class FAPCustomizationService
{
}

class FAPAssetGenerationService
{
}

class FAPSessionService
{
}

class FAPFontManager
{
}

require_once __DIR__ . '/../fotoartpuzzle.php';
