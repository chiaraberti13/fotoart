# PrestaShop Puzzle Customizer Plugin - Complete Technical Specifications

## Project Overview

**Plugin Name:** Puzzle Customizer  
**Target Platform:** PrestaShop 1.7.6.9  
**PHP Version:** 7.3.33  
**Language:** Italian (all UI, labels, messages must be in Italian)  
**Document Version:** 2.0 COMPLETE  
**Date:** October 15, 2025

## Executive Summary

Develop a complete PrestaShop module that replicates and improves the puzzle customization functionality from fotoartpuzzle.it. The plugin allows customers to personalize puzzles with their own images through an intuitive interface while providing administrators with a complete control panel to manage all customization options.

### Key Objectives

1. Create a fully functional puzzle configurator integrated into PrestaShop 1.7.6.9
2. Provide intuitive user experience with real-time preview and automatic validations
3. Implement a completely dynamic admin backend without hardcoded limits
4. Efficiently and securely manage high-resolution image files
5. Automatically generate production-ready files

### Core Features

| Component | Key Features |
|-----------|--------------|
| **Admin Backend** | Dynamic product management, unlimited dimensions, color picker for boxes/texts, font upload, image format management, order visualization |
| **User Frontend** | Drag & drop upload, HTML5 canvas editor, crop/zoom/rotate, 3D box preview, real-time validations, auto-save system |
| **Image Processing** | Multi-format support (JPEG, PNG, TIFF, WEBP, HEIC, RAW), automatic conversions, DPI optimization, watermark, production file generation |
| **Database** | 6 optimized tables: products, options, customizations, fonts, image formats, global configurations |
| **Security** | Rigorous MIME type validation, antivirus scan, input sanitization, CSRF tokens, XSS prevention, SQL injection protection |

**CRITICAL NOTE:** The plugin handles high-resolution files up to 50 MB. It's essential to properly configure PHP limits (memory_limit, upload_max_filesize, post_max_size) and implement asynchronous processing for very large images.

---

## 1. Plugin File Structure

```
puzzlecustomizer/
├── puzzlecustomizer.php                 # Main module file
├── config.xml
├── index.php                            # Security
├── logo.png                             # Module icon 200x200px
├── 
├── classes/
│   ├── PuzzleCustomization.php         # Main customization model
│   ├── PuzzleProduct.php               # Product association model
│   ├── PuzzleOption.php                # Dimensions/pieces model
│   ├── PuzzleFont.php                  # Font management model
│   ├── PuzzleImageFormat.php           # Image format model (NEW)
│   ├── PuzzleBoxColor.php              # Box color model
│   ├── PuzzleTextColor.php             # Text color model
│   ├── ImageProcessor.php              # Image processing engine
│   └── index.php                       # Security
│
├── controllers/
│   ├── front/
│   │   ├── Customizer.php              # Main customizer page
│   │   ├── Upload.php                  # Image upload handler
│   │   ├── Preview.php                 # 3D preview generator
│   │   ├── SaveConfig.php              # Save configuration AJAX
│   │   └── index.php                   # Security
│   │
│   └── admin/
│       ├── AdminPuzzleProductsController.php
│       ├── AdminPuzzleOptionsController.php
│       ├── AdminPuzzleBoxColorsController.php
│       ├── AdminPuzzleTextColorsController.php
│       ├── AdminPuzzleFontsController.php
│       ├── AdminPuzzleImageFormatsController.php    # NEW
│       ├── AdminPuzzleOrdersController.php
│       ├── AdminPuzzleConfigurationController.php   # Global settings
│       └── index.php                                # Security
│
├── views/
│   ├── templates/
│   │   ├── front/
│   │   │   ├── customizer.tpl          # Main customizer interface
│   │   │   ├── upload.tpl              # Upload interface
│   │   │   ├── editor.tpl              # Canvas editor
│   │   │   ├── options.tpl             # Options selector
│   │   │   ├── preview.tpl             # 3D preview
│   │   │   └── index.php               # Security
│   │   │
│   │   └── admin/
│   │       ├── products.tpl
│   │       ├── options.tpl
│   │       ├── fonts.tpl
│   │       ├── formats.tpl             # NEW
│   │       ├── orders.tpl
│   │       └── index.php               # Security
│   │
│   ├── js/
│   │   ├── front/
│   │   │   ├── customizer.js           # Main frontend logic
│   │   │   ├── upload-handler.js       # Dropzone implementation
│   │   │   ├── canvas-editor.js        # Fabric.js editor
│   │   │   ├── preview-3d.js           # Three.js 3D preview
│   │   │   ├── validations.js          # Client-side validations
│   │   │   └── index.php               # Security
│   │   │
│   │   └── admin/
│   │       ├── admin-panel.js
│   │       ├── color-picker.js         # Spectrum.js integration
│   │       ├── font-uploader.js
│   │       └── index.php               # Security
│   │
│   └── css/
│       ├── front/
│       │   ├── customizer.css
│       │   ├── editor.css
│       │   └── index.php               # Security
│       │
│       └── admin/
│           ├── admin-panel.css
│           └── index.php               # Security
│
├── fonts/                               # Uploadable fonts directory
│   └── index.php                       # Security
│
├── uploads/
│   ├── temp/                           # Temporary uploads
│   │   └── index.php                   # Security
│   │
│   └── customizations/                 # Final customizations by order ID
│       └── index.php                   # Security
│
├── libs/                                # External libraries
│   ├── fabric.min.js                   # v5.x Canvas editor
│   ├── dropzone.min.js                 # File upload
│   ├── cropper.min.js                  # Image cropping
│   ├── spectrum.min.js                 # Color picker
│   ├── three.min.js                    # 3D preview (optional)
│   └── index.php                       # Security
│
├── translations/
│   ├── it.php                          # Italian translations
│   └── index.php                       # Security
│
├── sql/
│   ├── install.php                     # Database installation
│   ├── uninstall.php                   # Database cleanup
│   └── index.php                       # Security
│
└── docs/
    ├── README.md
    ├── INSTALLATION.md
    └── API.md
```

---

## 2. Database Schema - Complete Specifications

### 2.1 Table: `ps_puzzle_products`
Associates PrestaShop products with customization functionality

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_puzzle_products` (
  `id_puzzle_product` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_product` INT(11) UNSIGNED NOT NULL,
  `is_customizable` TINYINT(1) NOT NULL DEFAULT 1,
  `min_resolution_width` INT(11) NULL DEFAULT NULL COMMENT 'Minimum width in pixels',
  `min_resolution_height` INT(11) NULL DEFAULT NULL COMMENT 'Minimum height in pixels',
  `recommended_dpi` INT(11) NULL DEFAULT 300 COMMENT 'Recommended DPI for printing',
  `instructions` TEXT NULL DEFAULT NULL COMMENT 'Custom instructions for customer',
  `template_image` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Example template path',
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_puzzle_product`),
  UNIQUE KEY `id_product` (`id_product`),
  KEY `is_customizable` (`is_customizable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2 Table: `ps_puzzle_options`
Stores all puzzle dimensions and piece counts (UNLIMITED)

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_puzzle_options` (
  `id_puzzle_option` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_puzzle_product` INT(11) UNSIGNED NOT NULL,
  `pieces` INT(11) NOT NULL COMMENT 'Number of pieces (e.g., 100, 500, 1000)',
  `width_cm` DECIMAL(10,2) NOT NULL COMMENT 'Width in centimeters',
  `height_cm` DECIMAL(10,2) NOT NULL COMMENT 'Height in centimeters',
  `price_impact` DECIMAL(20,6) NOT NULL DEFAULT 0.000000 COMMENT 'Price increment',
  `difficulty` ENUM('very_easy','easy','medium_easy','medium','difficult','very_difficult','extreme') NOT NULL DEFAULT 'medium',
  `recommended_age` VARCHAR(50) NULL DEFAULT NULL COMMENT 'e.g., Children 6+, Adults',
  `min_resolution_width` INT(11) NULL DEFAULT NULL COMMENT 'Auto-calculated or manual',
  `min_resolution_height` INT(11) NULL DEFAULT NULL COMMENT 'Auto-calculated or manual',
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `position` INT(11) NOT NULL DEFAULT 0 COMMENT 'Sort order',
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_puzzle_option`),
  KEY `id_puzzle_product` (`id_puzzle_product`),
  KEY `active` (`active`),
  KEY `position` (`position`),
  CONSTRAINT `fk_puzzle_option_product` FOREIGN KEY (`id_puzzle_product`) 
    REFERENCES `PREFIX_puzzle_products` (`id_puzzle_product`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.3 Table: `ps_puzzle_box_colors`
Stores box colors with RGB values (UNLIMITED)

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_puzzle_box_colors` (
  `id_box_color` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT 'Color name in Italian (e.g., Oro Metallizzato)',
  `hex_code` VARCHAR(7) NOT NULL COMMENT 'HEX color code #RRGGBB',
  `rgb_r` TINYINT(3) UNSIGNED NOT NULL,
  `rgb_g` TINYINT(3) UNSIGNED NOT NULL,
  `rgb_b` TINYINT(3) UNSIGNED NOT NULL,
  `price_impact` DECIMAL(20,6) NOT NULL DEFAULT 0.000000,
  `texture_image` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Optional texture for 3D preview',
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `position` INT(11) NOT NULL DEFAULT 0,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_box_color`),
  KEY `active` (`active`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.4 Table: `ps_puzzle_text_colors`
Stores text colors for personalization (UNLIMITED or RGB selector)

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_puzzle_text_colors` (
  `id_text_color` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT 'Color name in Italian',
  `hex_code` VARCHAR(7) NOT NULL COMMENT 'HEX color code #RRGGBB',
  `rgb_r` TINYINT(3) UNSIGNED NOT NULL,
  `rgb_g` TINYINT(3) UNSIGNED NOT NULL,
  `rgb_b` TINYINT(3) UNSIGNED NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `position` INT(11) NOT NULL DEFAULT 0,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_text_color`),
  KEY `active` (`active`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.5 Table: `ps_puzzle_fonts`
Stores uploadable fonts for text customization

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_puzzle_fonts` (
  `id_font` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT 'Font display name',
  `file_name` VARCHAR(255) NOT NULL COMMENT 'Font file name (e.g., arial.ttf)',
  `file_path` VARCHAR(500) NOT NULL COMMENT 'Full path to font file',
  `category` ENUM('serif','sans-serif','script','monospace','display') NOT NULL DEFAULT 'sans-serif',
  `preview_image` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Generated preview image path',
  `preview_text` VARCHAR(255) NOT NULL DEFAULT 'Il tuo testo personalizzato',
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `position` INT(11) NOT NULL DEFAULT 0,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_font`),
  UNIQUE KEY `file_name` (`file_name`),
  KEY `active` (`active`),
  KEY `position` (`position`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.6 Table: `ps_puzzle_image_formats` (NEW)
Dynamically manages accepted image formats with automatic conversions

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_puzzle_image_formats` (
  `id_format` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL COMMENT 'Format name (e.g., JPEG, PNG, HEIC)',
  `extensions` VARCHAR(255) NOT NULL COMMENT 'Comma-separated extensions (e.g., jpg,jpeg)',
  `mime_types` VARCHAR(500) NOT NULL COMMENT 'Comma-separated MIME types',
  `max_size_mb` INT(11) NOT NULL DEFAULT 20 COMMENT 'Max file size in MB',
  `recommended_dpi` INT(11) NOT NULL DEFAULT 300,
  `min_resolution_width` INT(11) NULL DEFAULT NULL,
  `min_resolution_height` INT(11) NULL DEFAULT NULL,
  `auto_convert_to` VARCHAR(10) NULL DEFAULT NULL COMMENT 'Target format for conversion (e.g., JPEG, PNG)',
  `requires_conversion` TINYINT(1) NOT NULL DEFAULT 0,
  `technical_notes` TEXT NULL DEFAULT NULL COMMENT 'Notes shown to users',
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `position` INT(11) NOT NULL DEFAULT 0,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_format`),
  KEY `active` (`active`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.7 Table: `ps_puzzle_customizations`
Stores customer customizations linked to orders

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_puzzle_customizations` (
  `id_customization` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cart` INT(11) UNSIGNED NOT NULL,
  `id_product` INT(11) UNSIGNED NOT NULL,
  `id_product_attribute` INT(11) UNSIGNED NULL DEFAULT 0,
  `id_order` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Populated after order creation',
  `id_customer` INT(11) UNSIGNED NOT NULL,
  
  -- Selected Options
  `id_puzzle_option` INT(11) UNSIGNED NULL DEFAULT NULL,
  `id_box_color` INT(11) UNSIGNED NULL DEFAULT NULL,
  `id_text_color` INT(11) UNSIGNED NULL DEFAULT NULL,
  `id_font` INT(11) UNSIGNED NULL DEFAULT NULL,
  
  -- Image Data
  `original_image_path` VARCHAR(500) NOT NULL COMMENT 'Original uploaded image',
  `original_image_name` VARCHAR(255) NOT NULL,
  `original_image_size` INT(11) NOT NULL COMMENT 'Size in bytes',
  `original_image_format` VARCHAR(20) NOT NULL,
  `processed_image_path` VARCHAR(500) NULL DEFAULT NULL COMMENT 'Processed/converted image',
  `image_width` INT(11) NOT NULL,
  `image_height` INT(11) NOT NULL,
  `image_dpi` INT(11) NULL DEFAULT NULL,
  
  -- Canvas Configuration (JSON)
  `canvas_config` TEXT NULL DEFAULT NULL COMMENT 'JSON: zoom, rotation, crop coordinates, filters',
  
  -- Text Customization
  `custom_text` TEXT NULL DEFAULT NULL COMMENT 'User-added text',
  `text_font_size` INT(11) NULL DEFAULT NULL,
  `text_position_x` INT(11) NULL DEFAULT NULL,
  `text_position_y` INT(11) NULL DEFAULT NULL,
  
  -- Production Files
  `production_files_generated` TINYINT(1) NOT NULL DEFAULT 0,
  `production_zip_path` VARCHAR(500) NULL DEFAULT NULL,
  
  -- Status
  `validation_passed` TINYINT(1) NOT NULL DEFAULT 0,
  `validation_warnings` TEXT NULL DEFAULT NULL COMMENT 'JSON array of warnings',
  
  -- Timestamps
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  
  PRIMARY KEY (`id_customization`),
  KEY `id_cart` (`id_cart`),
  KEY `id_order` (`id_order`),
  KEY `id_product` (`id_product`),
  KEY `id_customer` (`id_customer`),
  KEY `date_add` (`date_add`),
  CONSTRAINT `fk_customization_puzzle_option` FOREIGN KEY (`id_puzzle_option`) 
    REFERENCES `PREFIX_puzzle_options` (`id_puzzle_option`) ON DELETE SET NULL,
  CONSTRAINT `fk_customization_box_color` FOREIGN KEY (`id_box_color`) 
    REFERENCES `PREFIX_puzzle_box_colors` (`id_box_color`) ON DELETE SET NULL,
  CONSTRAINT `fk_customization_text_color` FOREIGN KEY (`id_text_color`) 
    REFERENCES `PREFIX_puzzle_text_colors` (`id_text_color`) ON DELETE SET NULL,
  CONSTRAINT `fk_customization_font` FOREIGN KEY (`id_font`) 
    REFERENCES `PREFIX_puzzle_fonts` (`id_font`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.8 Table: `ps_puzzle_configuration`
Global module configuration settings

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_puzzle_configuration` (
  `id_config` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `config_key` VARCHAR(100) NOT NULL,
  `config_value` TEXT NULL DEFAULT NULL,
  `config_type` ENUM('string','int','float','boolean','json') NOT NULL DEFAULT 'string',
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Default Configuration Keys:**
- `max_upload_size_mb` (int) - Default: 50
- `auto_convert_formats` (boolean) - Default: true
- `enable_rgb_text_selector` (boolean) - Default: false
- `dpi_warning_threshold` (int) - Default: 200
- `block_low_resolution` (boolean) - Default: false
- `enable_antivirus_scan` (boolean) - Default: true
- `watermark_enabled` (boolean) - Default: false
- `watermark_text` (string) - Default: empty
- `enable_3d_preview` (boolean) - Default: true
- `auto_save_interval_seconds` (int) - Default: 30

---

## 3. Main Module File: puzzlecustomizer.php

```php
<?php
/**
 * Puzzle Customizer Module for PrestaShop 1.7.6.9
 * 
 * @author Your Name
 * @version 2.0.0
 * @license Commercial
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

// Autoload module classes
require_once _PS_MODULE_DIR_ . 'puzzlecustomizer/classes/PuzzleCustomization.php';
require_once _PS_MODULE_DIR_ . 'puzzlecustomizer/classes/PuzzleProduct.php';
require_once _PS_MODULE_DIR_ . 'puzzlecustomizer/classes/PuzzleOption.php';
require_once _PS_MODULE_DIR_ . 'puzzlecustomizer/classes/PuzzleFont.php';
require_once _PS_MODULE_DIR_ . 'puzzlecustomizer/classes/PuzzleImageFormat.php';
require_once _PS_MODULE_DIR_ . 'puzzlecustomizer/classes/PuzzleBoxColor.php';
require_once _PS_MODULE_DIR_ . 'puzzlecustomizer/classes/PuzzleTextColor.php';
require_once _PS_MODULE_DIR_ . 'puzzlecustomizer/classes/ImageProcessor.php';

class PuzzleCustomizer extends Module
{
    public function __construct()
    {
        $this->name = 'puzzlecustomizer';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'Your Company';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => '1.7.9.99'
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Puzzle Customizer');
        $this->description = $this->l('Consente ai clienti di personalizzare puzzle con le proprie immagini');
        $this->confirmUninstall = $this->l('Sei sicuro di voler disinstallare questo modulo?');
    }

    /**
     * Module installation
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Create database tables
        if (!$this->createTables()) {
            return false;
        }

        // Install default image formats
        if (!$this->installDefaultImageFormats()) {
            return false;
        }

        // Register hooks
        if (!$this->registerHook('displayProductAdditionalInfo') ||
            !$this->registerHook('actionProductUpdate') ||
            !$this->registerHook('actionCartSave') ||
            !$this->registerHook('actionValidateOrder') ||
            !$this->registerHook('displayAdminProductsExtra') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('displayHeader')
        ) {
            return false;
        }

        // Create admin tabs
        if (!$this->installTabs()) {
            return false;
        }

        // Create directories
        if (!$this->createDirectories()) {
            return false;
        }

        // Install default configuration
        if (!$this->installDefaultConfiguration()) {
            return false;
        }

        return true;
    }

    /**
     * Module uninstallation
     */
    public function uninstall()
    {
        // Drop tables
        if (!$this->dropTables()) {
            return false;
        }

        // Remove admin tabs
        if (!$this->uninstallTabs()) {
            return false;
        }

        // Remove configuration
        if (!$this->uninstallConfiguration()) {
            return false;
        }

        return parent::uninstall();
    }

    /**
     * Create database tables
     */
    private function createTables()
    {
        $sql = [];

        // Include all CREATE TABLE statements from Section 2
        include(_PS_MODULE_DIR_ . 'puzzlecustomizer/sql/install.php');

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Drop database tables
     */
    private function dropTables()
    {
        $sql = [];
        
        include(_PS_MODULE_DIR_ . 'puzzlecustomizer/sql/uninstall.php');

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Install admin tabs
     */
    private function installTabs()
    {
        $tabs = [
            [
                'class_name' => 'AdminPuzzleCustomizer',
                'name' => 'Puzzle Customizer',
                'parent' => 'SELL',
                'icon' => 'extension'
            ],
            [
                'class_name' => 'AdminPuzzleProducts',
                'name' => 'Prodotti Personalizzabili',
                'parent' => 'AdminPuzzleCustomizer'
            ],
            [
                'class_name' => 'AdminPuzzleOptions',
                'name' => 'Opzioni Puzzle',
                'parent' => 'AdminPuzzleCustomizer'
            ],
            [
                'class_name' => 'AdminPuzzleBoxColors',
                'name' => 'Colori Scatola',
                'parent' => 'AdminPuzzleCustomizer'
            ],
            [
                'class_name' => 'AdminPuzzleTextColors',
                'name' => 'Colori Testo',
                'parent' => 'AdminPuzzleCustomizer'
            ],
            [
                'class_name' => 'AdminPuzzleFonts',
                'name' => 'Font Personalizzati',
                'parent' => 'AdminPuzzleCustomizer'
            ],
            [
                'class_name' => 'AdminPuzzleImageFormats',
                'name' => 'Formati Immagine',
                'parent' => 'AdminPuzzleCustomizer'
            ],
            [
                'class_name' => 'AdminPuzzleOrders',
                'name' => 'Ordini Personalizzati',
                'parent' => 'AdminPuzzleCustomizer'
            ],
            [
                'class_name' => 'AdminPuzzleConfiguration',
                'name' => 'Configurazione',
                'parent' => 'AdminPuzzleCustomizer'
            ]
        ];

        foreach ($tabs as $tabData) {
            $tab = new Tab();
            $tab->class_name = $tabData['class_name'];
            $tab->module = $this->name;
            $tab->id_parent = (int)Tab::getIdFromClassName($tabData['parent']);
            
            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[(int)$lang['id_lang']] = $tabData['name'];
            }

            if (isset($tabData['icon'])) {
                $tab->icon = $tabData['icon'];
            }

            if (!$tab->add()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Uninstall admin tabs
     */
    private function uninstallTabs()
    {
        $tabClasses = [
            'AdminPuzzleCustomizer',
            'AdminPuzzleProducts',
            'AdminPuzzleOptions',
            'AdminPuzzleBoxColors',
            'AdminPuzzleTextColors',
            'AdminPuzzleFonts',
            'AdminPuzzleImageFormats',
            'AdminPuzzleOrders',
            'AdminPuzzleConfiguration'
        ];

        foreach ($tabClasses as $tabClass) {
            $idTab = (int)Tab::getIdFromClassName($tabClass);
            if ($idTab) {
                $tab = new Tab($idTab);
                if (!$tab->delete()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Create necessary directories
     */
    private function createDirectories()
    {
        $directories = [
            _PS_MODULE_DIR_ . 'puzzlecustomizer/fonts/',
            _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/',
            _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/temp/',
            _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/customizations/'
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    return false;
                }
                
                // Add index.php for security
                file_put_contents($dir . 'index.php', '<?php header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); header("Cache-Control: no-cache"); header("Pragma: no-cache"); exit;');
            }
        }

        return true;
    }

    /**
     * Install default configuration
     */
    private function installDefaultConfiguration()
    {
        $configs = [
            'PUZZLE_MAX_UPLOAD_SIZE' => 50,
            'PUZZLE_AUTO_CONVERT' => 1,
            'PUZZLE_ENABLE_RGB_SELECTOR' => 0,
            'PUZZLE_DPI_WARNING' => 200,
            'PUZZLE_BLOCK_LOW_RES' => 0,
            'PUZZLE_ANTIVIRUS_SCAN' => 1,
            'PUZZLE_WATERMARK_ENABLED' => 0,
            'PUZZLE_WATERMARK_TEXT' => '',
            'PUZZLE_3D_PREVIEW' => 1,
            'PUZZLE_AUTOSAVE_INTERVAL' => 30
        ];

        foreach ($configs as $key => $value) {
            if (!Configuration::updateValue($key, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Uninstall configuration
     */
    private function uninstallConfiguration()
    {
        $configs = [
            'PUZZLE_MAX_UPLOAD_SIZE',
            'PUZZLE_AUTO_CONVERT',
            'PUZZLE_ENABLE_RGB_SELECTOR',
            'PUZZLE_DPI_WARNING',
            'PUZZLE_BLOCK_LOW_RES',
            'PUZZLE_ANTIVIRUS_SCAN',
            'PUZZLE_WATERMARK_ENABLED',
            'PUZZLE_WATERMARK_TEXT',
            'PUZZLE_3D_PREVIEW',
            'PUZZLE_AUTOSAVE_INTERVAL'
        ];

        foreach ($configs as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Install default image formats
     */
    private function installDefaultImageFormats()
    {
        $formats = [
            [
                'name' => 'JPEG/JPG',
                'extensions' => 'jpg,jpeg',
                'mime_types' => 'image/jpeg,image/jpg',
                'max_size_mb' => 20,
                'recommended_dpi' => 300,
                'auto_convert_to' => null,
                'requires_conversion' => 0,
                'technical_notes' => 'Formato standard per fotografie.',
                'active' => 1,
                'position' => 1
            ],
            [
                'name' => 'PNG',
                'extensions' => 'png',
                'mime_types' => 'image/png',
                'max_size_mb' => 25,
                'recommended_dpi' => 300,
                'auto_convert_to' => null,
                'requires_conversion' => 0,
                'technical_notes' => 'Supporta trasparenza, ideale per grafica.',
                'active' => 1,
                'position' => 2
            ],
            [
                'name' => 'TIFF/TIF',
                'extensions' => 'tiff,tif',
                'mime_types' => 'image/tiff,image/tif',
                'max_size_mb' => 50,
                'recommended_dpi' => 600,
                'auto_convert_to' => null,
                'requires_conversion' => 0,
                'technical_notes' => 'Formato professionale alta qualità.',
                'active' => 1,
                'position' => 3
            ],
            [
                'name' => 'WEBP',
                'extensions' => 'webp',
                'mime_types' => 'image/webp',
                'max_size_mb' => 20,
                'recommended_dpi' => 300,
                'auto_convert_to' => null,
                'requires_conversion' => 0,
                'technical_notes' => 'Formato moderno con buona compressione.',
                'active' => 1,
                'position' => 4
            ],
            [
                'name' => 'HEIC/HEIF',
                'extensions' => 'heic,heif',
                'mime_types' => 'image/heic,image/heif',
                'max_size_mb' => 20,
                'recommended_dpi' => 300,
                'auto_convert_to' => 'JPEG',
                'requires_conversion' => 1,
                'technical_notes' => 'Formato iPhone, verrà convertito automaticamente in JPEG.',
                'active' => 1,
                'position' => 5
            ],
            [
                'name' => 'BMP',
                'extensions' => 'bmp',
                'mime_types' => 'image/bmp,image/x-bmp',
                'max_size_mb' => 30,
                'recommended_dpi' => 300,
                'auto_convert_to' => 'PNG',
                'requires_conversion' => 1,
                'technical_notes' => 'Verrà convertito in PNG per ottimizzare dimensioni.',
                'active' => 1,
                'position' => 6
            ],
            [
                'name' => 'GIF',
                'extensions' => 'gif',
                'mime_types' => 'image/gif',
                'max_size_mb' => 15,
                'recommended_dpi' => 150,
                'auto_convert_to' => null,
                'requires_conversion' => 0,
                'technical_notes' => 'Non consigliato per foto, bassa qualità colore.',
                'active' => 0,
                'position' => 7
            ]
        ];

        foreach ($formats as $format) {
            $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'puzzle_image_formats` 
                    (`name`, `extensions`, `mime_types`, `max_size_mb`, `recommended_dpi`, 
                     `auto_convert_to`, `requires_conversion`, `technical_notes`, `active`, `position`, `date_add`, `date_upd`)
                    VALUES 
                    ("' . pSQL($format['name']) . '", 
                     "' . pSQL($format['extensions']) . '", 
                     "' . pSQL($format['mime_types']) . '", 
                     ' . (int)$format['max_size_mb'] . ', 
                     ' . (int)$format['recommended_dpi'] . ', 
                     ' . ($format['auto_convert_to'] ? '"' . pSQL($format['auto_convert_to']) . '"' : 'NULL') . ', 
                     ' . (int)$format['requires_conversion'] . ', 
                     "' . pSQL($format['technical_notes']) . '", 
                     ' . (int)$format['active'] . ', 
                     ' . (int)$format['position'] . ', 
                     NOW(), 
                     NOW())';
            
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Hook: Display customizer button on product page
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        $product = $params['product'];
        
        // Check if product is customizable
        $isCustomizable = Db::getInstance()->getValue('
            SELECT is_customizable 
            FROM `' . _DB_PREFIX_ . 'puzzle_products` 
            WHERE id_product = ' . (int)$product['id_product'] . ' 
            AND is_customizable = 1
        ');

        if (!$isCustomizable) {
            return '';
        }

        $this->context->smarty->assign([
            'puzzle_customizer_url' => $this->context->link->getModuleLink(
                'puzzlecustomizer',
                'customizer',
                ['id_product' => (int)$product['id_product']]
            ),
            'product_name' => $product['name']
        ]);

        return $this->display(__FILE__, 'views/templates/front/product-button.tpl');
    }

    /**
     * Hook: Add CSS/JS to front header
     */
    public function hookDisplayHeader()
    {
        // Only on customizer page
        if ($this->context->controller->php_self === 'module-puzzlecustomizer-customizer') {
            // CSS
            $this->context->controller->addCSS($this->_path . 'views/css/front/customizer.css');
            $this->context->controller->addCSS($this->_path . 'views/css/front/editor.css');
            
            // External libraries CSS
            $this->context->controller->addCSS($this->_path . 'libs/dropzone.min.css');
            $this->context->controller->addCSS($this->_path . 'libs/cropper.min.css');
            $this->context->controller->addCSS($this->_path . 'libs/spectrum.min.css');

            // JavaScript libraries
            $this->context->controller->addJS($this->_path . 'libs/fabric.min.js');
            $this->context->controller->addJS($this->_path . 'libs/dropzone.min.js');
            $this->context->controller->addJS($this->_path . 'libs/cropper.min.js');
            $this->context->controller->addJS($this->_path . 'libs/spectrum.min.js');

            // Module JS
            $this->context->controller->addJS($this->_path . 'views/js/front/customizer.js');
            $this->context->controller->addJS($this->_path . 'views/js/front/upload-handler.js');
            $this->context->controller->addJS($this->_path . 'views/js/front/canvas-editor.js');
            $this->context->controller->addJS($this->_path . 'views/js/front/validations.js');
            
            if (Configuration::get('PUZZLE_3D_PREVIEW')) {
                $this->context->controller->addJS($this->_path . 'libs/three.min.js');
                $this->context->controller->addJS($this->_path . 'views/js/front/preview-3d.js');
            }
        }
    }

    /**
     * Hook: Add CSS/JS to admin header
     */
    public function hookDisplayBackOfficeHeader()
    {
        // Only on module admin pages
        if (strpos($this->context->controller->controller_name, 'AdminPuzzle') !== false) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin/admin-panel.css');
            $this->context->controller->addCSS($this->_path . 'libs/spectrum.min.css');
            
            $this->context->controller->addJS($this->_path . 'views/js/admin/admin-panel.js');
            $this->context->controller->addJS($this->_path . 'libs/spectrum.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/admin/color-picker.js');
            $this->context->controller->addJS($this->_path . 'views/js/admin/font-uploader.js');
        }
    }

    /**
     * Module configuration page
     */
    public function getContent()
    {
        $output = '';

        // Handle form submission
        if (Tools::isSubmit('submitPuzzleConfiguration')) {
            Configuration::updateValue('PUZZLE_MAX_UPLOAD_SIZE', (int)Tools::getValue('PUZZLE_MAX_UPLOAD_SIZE'));
            Configuration::updateValue('PUZZLE_AUTO_CONVERT', (int)Tools::getValue('PUZZLE_AUTO_CONVERT'));
            Configuration::updateValue('PUZZLE_ENABLE_RGB_SELECTOR', (int)Tools::getValue('PUZZLE_ENABLE_RGB_SELECTOR'));
            Configuration::updateValue('PUZZLE_DPI_WARNING', (int)Tools::getValue('PUZZLE_DPI_WARNING'));
            Configuration::updateValue('PUZZLE_BLOCK_LOW_RES', (int)Tools::getValue('PUZZLE_BLOCK_LOW_RES'));
            Configuration::updateValue('PUZZLE_ANTIVIRUS_SCAN', (int)Tools::getValue('PUZZLE_ANTIVIRUS_SCAN'));
            Configuration::updateValue('PUZZLE_WATERMARK_ENABLED', (int)Tools::getValue('PUZZLE_WATERMARK_ENABLED'));
            Configuration::updateValue('PUZZLE_WATERMARK_TEXT', pSQL(Tools::getValue('PUZZLE_WATERMARK_TEXT')));
            Configuration::updateValue('PUZZLE_3D_PREVIEW', (int)Tools::getValue('PUZZLE_3D_PREVIEW'));
            Configuration::updateValue('PUZZLE_AUTOSAVE_INTERVAL', (int)Tools::getValue('PUZZLE_AUTOSAVE_INTERVAL'));

            $output .= $this->displayConfirmation($this->l('Impostazioni salvate con successo'));
        }

        return $output . $this->renderConfigForm();
    }

    /**
     * Render configuration form
     */
    protected function renderConfigForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPuzzleConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Configuration form structure
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configurazione Globale'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Dimensione massima upload (MB)'),
                        'name' => 'PUZZLE_MAX_UPLOAD_SIZE',
                        'size' => 10,
                        'required' => true,
                        'desc' => $this->l('Dimensione massima file immagine (1-100 MB)')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Conversione automatica formati'),
                        'name' => 'PUZZLE_AUTO_CONVERT',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Sì')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')]
                        ],
                        'desc' => $this->l('Converti automaticamente formati non standard in JPEG')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Selettore RGB personalizzato'),
                        'name' => 'PUZZLE_ENABLE_RGB_SELECTOR',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'rgb_on', 'value' => 1, 'label' => $this->l('Sì')],
                            ['id' => 'rgb_off', 'value' => 0, 'label' => $this->l('No')]
                        ],
                        'desc' => $this->l('Permetti agli utenti di scegliere qualsiasi colore RGB per il testo')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Soglia avviso DPI'),
                        'name' => 'PUZZLE_DPI_WARNING',
                        'size' => 10,
                        'required' => true,
                        'desc' => $this->l('Mostra avviso se DPI inferiore a questo valore (consigliato: 200-300)')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Blocca risoluzione insufficiente'),
                        'name' => 'PUZZLE_BLOCK_LOW_RES',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'block_on', 'value' => 1, 'label' => $this->l('Sì')],
                            ['id' => 'block_off', 'value' => 0, 'label' => $this->l('No')]
                        ],
                        'desc' => $this->l('Impedisci upload se risoluzione troppo bassa')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Scansione antivirus'),
                        'name' => 'PUZZLE_ANTIVIRUS_SCAN',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'av_on', 'value' => 1, 'label' => $this->l('Sì')],
                            ['id' => 'av_off', 'value' => 0, 'label' => $this->l('No')]
                        ],
                        'desc' => $this->l('Esegui scansione antivirus su file caricati (richiede ClamAV)')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Abilita watermark'),
                        'name' => 'PUZZLE_WATERMARK_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'wm_on', 'value' => 1, 'label' => $this->l('Sì')],
                            ['id' => 'wm_off', 'value' => 0, 'label' => $this->l('No')]
                        ],
                        'desc' => $this->l('Aggiungi watermark alle immagini in preview')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Testo watermark'),
                        'name' => 'PUZZLE_WATERMARK_TEXT',
                        'size' => 50,
                        'desc' => $this->l('Testo da usare come watermark (lasciare vuoto per logo)')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Preview 3D scatola'),
                        'name' => 'PUZZLE_3D_PREVIEW',
                        'is_bool' => true,
                        'values' => [
                            ['id' => '3d_on', 'value' => 1, 'label' => $this->l('Sì')],
                            ['id' => '3d_off', 'value' => 0, 'label' => $this->l('No')]
                        ],
                        'desc' => $this->l('Mostra anteprima 3D della scatola personalizzata')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Intervallo salvataggio automatico (secondi)'),
                        'name' => 'PUZZLE_AUTOSAVE_INTERVAL',
                        'size' => 10,
                        'required' => true,
                        'desc' => $this->l('Frequenza salvataggio automatico configurazione (10-120 secondi)')
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Salva'),
                ],
            ],
        ];
    }

    /**
     * Get configuration form values
     */
    protected function getConfigFormValues()
    {
        return [
            'PUZZLE_MAX_UPLOAD_SIZE' => Configuration::get('PUZZLE_MAX_UPLOAD_SIZE'),
            'PUZZLE_AUTO_CONVERT' => Configuration::get('PUZZLE_AUTO_CONVERT'),
            'PUZZLE_ENABLE_RGB_SELECTOR' => Configuration::get('PUZZLE_ENABLE_RGB_SELECTOR'),
            'PUZZLE_DPI_WARNING' => Configuration::get('PUZZLE_DPI_WARNING'),
            'PUZZLE_BLOCK_LOW_RES' => Configuration::get('PUZZLE_BLOCK_LOW_RES'),
            'PUZZLE_ANTIVIRUS_SCAN' => Configuration::get('PUZZLE_ANTIVIRUS_SCAN'),
            'PUZZLE_WATERMARK_ENABLED' => Configuration::get('PUZZLE_WATERMARK_ENABLED'),
            'PUZZLE_WATERMARK_TEXT' => Configuration::get('PUZZLE_WATERMARK_TEXT'),
            'PUZZLE_3D_PREVIEW' => Configuration::get('PUZZLE_3D_PREVIEW'),
            'PUZZLE_AUTOSAVE_INTERVAL' => Configuration::get('PUZZLE_AUTOSAVE_INTERVAL')
        ];
    }
}
```

---

## 4. Class: ImageProcessor.php

This is a critical class that handles all image processing operations.

```php
<?php
/**
 * Image Processing Engine
 * Handles upload, validation, conversion, and production file generation
 */

class ImageProcessor
{
    /**
     * Validate uploaded image
     * 
     * @param array $file $_FILES array
     * @param int $idFormat Format ID from database
     * @return array ['success' => bool, 'message' => string, 'warnings' => array]
     */
    public static function validateImage($file, $idFormat)
    {
        $result = [
            'success' => true,
            'message' => '',
            'warnings' => []
        ];

        // Get format configuration
        $format = Db::getInstance()->getRow('
            SELECT * FROM `' . _DB_PREFIX_ . 'puzzle_image_formats` 
            WHERE id_format = ' . (int)$idFormat . ' AND active = 1
        ');

        if (!$format) {
            return [
                'success' => false,
                'message' => 'Formato immagine non supportato',
                'warnings' => []
            ];
        }

        // Check file size
        $maxSizeBytes = $format['max_size_mb'] * 1024 * 1024;
        if ($file['size'] > $maxSizeBytes) {
            return [
                'success' => false,
                'message' => 'File troppo grande. Dimensione massima: ' . $format['max_size_mb'] . ' MB',
                'warnings' => []
            ];
        }

        // Validate MIME type
        $allowedMimes = explode(',', $format['mime_types']);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            return [
                'success' => false,
                'message' => 'Tipo file non valido. Previsto: ' . implode(', ', $allowedMimes),
                'warnings' => []
            ];
        }

        // Antivirus scan if enabled
        if (Configuration::get('PUZZLE_ANTIVIRUS_SCAN')) {
            if (!self::scanForVirus($file['tmp_name'])) {
                return [
                    'success' => false,
                    'message' => 'File sospetto rilevato dalla scansione antivirus',
                    'warnings' => []
                ];
            }
        }

        // Get image dimensions and DPI
        $imageInfo = self::getImageInfo($file['tmp_name'], $mimeType);
        
        if (!$imageInfo) {
            return [
                'success' => false,
                'message' => 'Impossibile leggere informazioni immagine',
                'warnings' => []
            ];
        }

        // Check minimum resolution
        if ($format['min_resolution_width'] && $imageInfo['width'] < $format['min_resolution_width']) {
            $result['warnings'][] = 'Larghezza immagine inferiore alla risoluzione consigliata';
        }

        if ($format['min_resolution_height'] && $imageInfo['height'] < $format['min_resolution_height']) {
            $result['warnings'][] = 'Altezza immagine inferiore alla risoluzione consigliata';
        }

        // Check DPI
        $dpiThreshold = Configuration::get('PUZZLE_DPI_WARNING');
        if ($imageInfo['dpi'] && $imageInfo['dpi'] < $dpiThreshold) {
            $result['warnings'][] = 'DPI basso (' . $imageInfo['dpi'] . '). Consigliato: ' . $format['recommended_dpi'];
        }

        // Block if resolution too low and blocking enabled
        if (Configuration::get('PUZZLE_BLOCK_LOW_RES') && !empty($result['warnings'])) {
            return [
                'success' => false,
                'message' => 'Risoluzione immagine insufficiente per la stampa',
                'warnings' => $result['warnings']
            ];
        }

        $result['image_info'] = $imageInfo;
        return $result;
    }

    /**
     * Get image information (dimensions, DPI, color space)
     * 
     * @param string $filePath
     * @param string $mimeType
     * @return array|false
     */
    public static function getImageInfo($filePath, $mimeType)
    {
        try {
            // Use ImageMagick if available
            if (extension_loaded('imagick')) {
                $imagick = new Imagick($filePath);
                
                $info = [
                    'width' => $imagick->getImageWidth(),
                    'height' => $imagick->getImageHeight(),
                    'dpi' => null,
                    'color_space' => $imagick->getImageColorspace(),
                    'format' => $imagick->getImageFormat()
                ];

                // Get DPI
                $resolution = $imagick->getImageResolution();
                $info['dpi'] = isset($resolution['x']) ? (int)$resolution['x'] : null;

                $imagick->clear();
                return $info;
            }
            
            // Fallback to GD
            $size = getimagesize($filePath);
            if (!$size) {
                return false;
            }

            $info = [
                'width' => $size[0],
                'height' => $size[1],
                'dpi' => null,
                'color_space' => 'RGB', // GD doesn't provide color space
                'format' => image_type_to_mime_type($size[2])
            ];

            // Try to extract DPI from EXIF
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($filePath);
                if (isset($exif['XResolution'])) {
                    $info['dpi'] = (int)$exif['XResolution'];
                }
            }

            return $info;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Process and save uploaded image
     * 
     * @param array $file $_FILES array
     * @param int $idCustomer
     * @param int $idCart
     * @return array ['success' => bool, 'path' => string, 'message' => string]
     */
    public static function processUpload($file, $idCustomer, $idCart)
    {
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'puzzle_' . $idCustomer . '_' . $idCart . '_' . time() . '_' . uniqid() . '.' . $extension;
        
        // Temporary path
        $tempPath = _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/temp/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $tempPath)) {
            return [
                'success' => false,
                'path' => null,
                'message' => 'Errore nel salvataggio del file'
            ];
        }

        // Check if conversion needed
        $format = self::getFormatByExtension($extension);
        
        if ($format && $format['requires_conversion'] && $format['auto_convert_to']) {
            $convertedPath = self::convertImage($tempPath, $format['auto_convert_to']);
            
            if ($convertedPath) {
                // Remove original
                unlink($tempPath);
                
                return [
                    'success' => true,
                    'path' => $convertedPath,
                    'message' => 'Immagine caricata e convertita in ' . $format['auto_convert_to'],
                    'converted' => true
                ];
            }
        }

        return [
            'success' => true,
            'path' => $tempPath,
            'message' => 'Immagine caricata con successo',
            'converted' => false
        ];
    }

    /**
     * Convert image to different format
     * 
     * @param string $sourcePath
     * @param string $targetFormat (JPEG, PNG, TIFF)
     * @return string|false New file path or false on error
     */
    public static function convertImage($sourcePath, $targetFormat)
    {
        try {
            if (!extension_loaded('imagick')) {
                return false;
            }

            $imagick = new Imagick($sourcePath);
            
            // Set format
            $targetFormat = strtoupper($targetFormat);
            $imagick->setImageFormat($targetFormat);

            // Optimize based on format
            if ($targetFormat === 'JPEG') {
                $imagick->setImageCompressionQuality(95);
                $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
            } elseif ($targetFormat === 'PNG') {
                $imagick->setImageCompressionQuality(95);
                $imagick->setImageCompression(Imagick::COMPRESSION_ZIP);
            }

            // Generate new filename
            $pathInfo = pathinfo($sourcePath);
            $extension = strtolower($targetFormat === 'JPEG' ? 'jpg' : $targetFormat);
            $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_converted.' . $extension;

            // Save
            $imagick->writeImage($newPath);
            $imagick->clear();

            return $newPath;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate production files (puzzle + box)
     * 
     * @param int $idCustomization
     * @return array ['success' => bool, 'zip_path' => string, 'message' => string]
     */
    public static function generateProductionFiles($idCustomization)
    {
        // Get customization data
        $customization = new PuzzleCustomization($idCustomization);
        
        if (!Validate::isLoadedObject($customization)) {
            return [
                'success' => false,
                'zip_path' => null,
                'message' => 'Personalizzazione non trovata'
            ];
        }

        // Create order directory
        $orderDir = _PS_MODULE_DIR_ . 'puzzlecustomizer/uploads/customizations/' . $customization->id_order . '/';
        if (!file_exists($orderDir)) {
            mkdir($orderDir, 0755, true);
        }

        $files = [];

        try {
            // 1. Copy original image
            $originalDest = $orderDir . 'original' . pathinfo($customization->original_image_path, PATHINFO_EXTENSION);
            copy($customization->original_image_path, $originalDest);
            $files[] = $originalDest;

            // 2. Generate puzzle print file (300 DPI TIFF)
            $puzzlePrint = self::generatePuzzlePrint($customization, $orderDir);
            if ($puzzlePrint) {
                $files[] = $puzzlePrint;
            }

            // 3. Generate box front (300 DPI TIFF)
            $boxFront = self::generateBoxFront($customization, $orderDir);
            if ($boxFront) {
                $files[] = $boxFront;
            }

            // 4. Generate box back (300 DPI TIFF)
            $boxBack = self::generateBoxBack($customization, $orderDir);
            if ($boxBack) {
                $files[] = $boxBack;
            }

            // 5. Generate config JSON
            $configJson = self::generateConfigJson($customization, $orderDir);
            if ($configJson) {
                $files[] = $configJson;
            }

            // 6. Generate production specs PDF
            $specsPdf = self::generateSpecsPdf($customization, $orderDir);
            if ($specsPdf) {
                $files[] = $specsPdf;
            }

            // Create ZIP
            $zipPath = $orderDir . 'production_' . $customization->id_order . '.zip';
            $zip = new ZipArchive();
            
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                foreach ($files as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();

                // Update customization record
                $customization->production_files_generated = 1;
                $customization->production_zip_path = $zipPath;
                $customization->save();

                return [
                    'success' => true,
                    'zip_path' => $zipPath,
                    'message' => 'File produzione generati con successo'
                ];
            }

            return [
                'success' => false,
                'zip_path' => null,
                'message' => 'Errore nella creazione dello ZIP'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'zip_path' => null,
                'message' => 'Errore: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate high-quality puzzle print file
     */
    private static function generatePuzzlePrint($customization, $outputDir)
    {
        if (!extension_loaded('imagick')) {
            return false;
        }

        try {
            $imagick = new Imagick($customization->processed_image_path ?: $customization->original_image_path);

            // Apply canvas transformations from JSON
            if ($customization->canvas_config) {
                $config = json_decode($customization->canvas_config, true);
                
                // Apply rotation
                if (isset($config['rotation']) && $config['rotation'] != 0) {
                    $imagick->rotateImage(new ImagickPixel('transparent'), $config['rotation']);
                }

                // Apply crop
                if (isset($config['crop'])) {
                    $imagick->cropImage(
                        $config['crop']['width'],
                        $config['crop']['height'],
                        $config['crop']['x'],
                        $config['crop']['y']
                    );
                }

                // Apply filters
                if (isset($config['filters'])) {
                    foreach ($config['filters'] as $filter) {
                        self::applyFilter($imagick, $filter);
                    }
                }
            }

            // Set to 300 DPI
            $imagick->setImageResolution(300, 300);
            $imagick->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);

            // Convert to TIFF
            $imagick->setImageFormat('TIFF');
            $imagick->setImageCompression(Imagick::COMPRESSION_LZW);

            // Add custom text if present
            if ($customization->custom_text) {
                $draw = new ImagickDraw();
                
                // Get font
                if ($customization->id_font) {
                    $font = new PuzzleFont($customization->id_font);
                    $draw->setFont($font->file_path);
                }
                
                $draw->setFontSize($customization->text_font_size ?: 48);
                
                // Get text color
                if ($customization->id_text_color) {
                    $textColor = new PuzzleTextColor($customization->id_text_color);
                    $draw->setFillColor($textColor->hex_code);
                }
                
                $imagick->annotateImage(
                    $draw,
                    $customization->text_position_x ?: 100,
                    $customization->text_position_y ?: 100,
                    0,
                    $customization->custom_text
                );
            }

            // Save
            $outputPath = $outputDir . 'puzzle-print-300dpi.tiff';
            $imagick->writeImage($outputPath);
            $imagick->clear();

            return $outputPath;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate box front print file
     */
    private static function generateBoxFront($customization, $outputDir)
    {
        // Similar to generatePuzzlePrint but with box template overlay
        // This would composite the puzzle image onto a box front template
        
        // Implementation details depend on your specific box design
        // You would need box templates stored in the module
        
        $outputPath = $outputDir . 'box-front-300dpi.tiff';
        
        // Placeholder implementation
        // TODO: Implement actual box front generation with template overlay
        
        return $outputPath;
    }

    /**
     * Generate box back print file
     */
    private static function generateBoxBack($customization, $outputDir)
    {
        // Generate back of box with product info, barcode, etc.
        
        $outputPath = $outputDir . 'box-back-300dpi.tiff';
        
        // Placeholder implementation
        // TODO: Implement actual box back generation
        
        return $outputPath;
    }

    /**
     * Generate configuration JSON
     */
    private static function generateConfigJson($customization, $outputDir)
    {
        $config = [
            'order_id' => $customization->id_order,
            'customer_id' => $customization->id_customer,
            'product_id' => $customization->id_product,
            'customization' => [
                'pieces' => null,
                'dimensions' => null,
                'box_color' => null,
                'custom_text' => $customization->custom_text,
                'text_color' => null,
                'font' => null
            ],
            'image' => [
                'original_filename' => $customization->original_image_name,
                'original_format' => $customization->original_image_format,
                'width' => $customization->image_width,
                'height' => $customization->image_height,
                'dpi' => $customization->image_dpi
            ],
            'canvas_config' => json_decode($customization->canvas_config, true),
            'generated_at' => date('Y-m-d H:i:s')
        ];

        // Get puzzle option details
        if ($customization->id_puzzle_option) {
            $option = new PuzzleOption($customization->id_puzzle_option);
            $config['customization']['pieces'] = $option->pieces;
            $config['customization']['dimensions'] = [
                'width_cm' => $option->width_cm,
                'height_cm' => $option->height_cm
            ];
        }

        // Get box color details
        if ($customization->id_box_color) {
            $boxColor = new PuzzleBoxColor($customization->id_box_color);
            $config['customization']['box_color'] = [
                'name' => $boxColor->name,
                'hex' => $boxColor->hex_code
            ];
        }

        // Get text color details
        if ($customization->id_text_color) {
            $textColor = new PuzzleTextColor($customization->id_text_color);
            $config['customization']['text_color'] = [
                'name' => $textColor->name,
                'hex' => $textColor->hex_code
            ];
        }

        // Get font details
        if ($customization->id_font) {
            $font = new PuzzleFont($customization->id_font);
            $config['customization']['font'] = [
                'name' => $font->name,
                'family' => $font->category
            ];
        }

        $outputPath = $outputDir . 'config.json';
        file_put_contents($outputPath, json_encode($config, JSON_PRETTY_PRINT));

        return $outputPath;
    }

    /**
     * Generate production specifications PDF
     */
    private static function generateSpecsPdf($customization, $outputDir)
    {
        // This would use a PDF library like TCPDF or FPDF
        // to generate a human-readable specifications document
        
        $outputPath = $outputDir . 'production-specs.pdf';
        
        // Placeholder implementation
        // TODO: Implement PDF generation with TCPDF or similar
        
        return $outputPath;
    }

    /**
     * Apply image filter
     */
    private static function applyFilter($imagick, $filterConfig)
    {
        switch ($filterConfig['type']) {
            case 'grayscale':
                $imagick->setImageType(Imagick::IMGTYPE_GRAYSCALE);
                break;
            
            case 'sepia':
                $imagick->sepiaToneImage(80);
                break;
            
            case 'brightness':
                $imagick->modulateImage($filterConfig['value'], 100, 100);
                break;
            
            case 'contrast':
                $imagick->contrastImage($filterConfig['value'] > 0);
                break;
            
            case 'saturation':
                $imagick->modulateImage(100, $filterConfig['value'], 100);
                break;
        }
    }

    /**
     * Scan file for viruses using ClamAV
     */
    private static function scanForVirus($filePath)
    {
        // Check if ClamAV is available
        if (!function_exists('exec')) {
            return true; // Skip if exec not available
        }

        $output = [];
        $returnVar = 0;
        
        @exec('clamscan --no-summary ' . escapeshellarg($filePath), $output, $returnVar);
        
        // Return value 0 means no virus found
        return $returnVar === 0;
    }

    /**
     * Get format by file extension
     */
    private static function getFormatByExtension($extension)
    {
        $extension = strtolower($extension);
        
        $result = Db::getInstance()->getRow('
            SELECT * FROM `' . _DB_PREFIX_ . 'puzzle_image_formats` 
            WHERE FIND_IN_SET("' . pSQL($extension) . '", extensions) 
            AND active = 1
        ');

        return $result ?: false;
    }
}
```

---

## 5. Frontend JavaScript: customizer.js

Main frontend controller that orchestrates the entire customization experience.

```javascript
/**
 * Puzzle Customizer - Main Frontend Controller
 * Handles the complete customization workflow
 */

var PuzzleCustomizer = (function() {
    'use strict';

    // Configuration
    var config = {
        uploadUrl: '/modules/puzzlecustomizer/controllers/front/Upload.php',
        saveUrl: '/modules/puzzlecustomizer/controllers/front/SaveConfig.php',
        previewUrl: '/modules/puzzlecustomizer/controllers/front/Preview.php',
        maxFileSize: 50, // MB
        allowedFormats: [], // Loaded dynamically
        autoSaveInterval: 30000, // 30 seconds
        canvas: null,
        dropzone: null
    };

    // State management
    var state = {
        uploadedImage: null,
        selectedOptions: {
            puzzle_option: null,
            box_color: null,
            text_color: null,
            font: null
        },
        customText: '',
        canvasConfig: {
            zoom: 1,
            rotation: 0,
            crop: null,
            filters: []
        },
        unsavedChanges: false
    };

    /**
     * Initialize customizer
     */
    function init() {
        console.log('[PuzzleCustomizer] Initializing...');
        
        // Load configuration from backend
        loadConfiguration();
        
        // Initialize upload interface
        initUploadInterface();
        
        // Initialize canvas editor
        initCanvasEditor();
        
        // Initialize options selectors
        initOptionsSelectors();
        
        // Initialize preview
        if (config.enable3DPreview) {
            initPreview3D();
        }
        
        // Initialize auto-save
        initAutoSave();
        
        // Event listeners
        attachEventListeners();
        
        console.log('[PuzzleCustomizer] Initialization complete');
    }

    /**
     * Load configuration from backend
     */
    function loadConfiguration() {
        // This would be populated server-side in the template
        config.allowedFormats = window.PUZZLE_ALLOWED_FORMATS || [];
        config.maxFileSize = window.PUZZLE_MAX_FILE_SIZE || 50;
        config.autoSaveInterval = (window.PUZZLE_AUTOSAVE_INTERVAL || 30) * 1000;
        config.enable3DPreview = window.PUZZLE_3D_PREVIEW || false;
        config.enableRGBSelector = window.PUZZLE_ENABLE_RGB_SELECTOR || false;
    }

    /**
     * Initialize upload interface (Dropzone.js)
     */
    function initUploadInterface() {
        var uploadArea = document.getElementById('upload-area');
        
        if (!uploadArea) {
            console.error('[PuzzleCustomizer] Upload area not found');
            return;
        }

        // Initialize Dropzone
        config.dropzone = new Dropzone('#upload-area', {
            url: config.uploadUrl,
            maxFilesize: config.maxFileSize,
            acceptedFiles: config.allowedFormats.join(','),
            maxFiles: 1,
            addRemoveLinks: true,
            dictDefaultMessage: 'Trascina qui la tua immagine o clicca per selezionare',
            dictFileTooBig: 'File troppo grande ({{filesize}}MB). Massimo: {{maxFilesize}}MB',
            dictInvalidFileType: 'Formato file non supportato',
            dictRemoveFile: 'Rimuovi',
            dictCancelUpload: 'Annulla',
            
            init: function() {
                this.on('sending', function(file, xhr, formData) {
                    formData.append('id_customer', window.PUZZLE_CUSTOMER_ID);
                    formData.append('id_cart', window.PUZZLE_CART_ID);
                    formData.append('id_product', window.PUZZLE_PRODUCT_ID);
                    
                    showProgress('Caricamento in corso...');
                });

                this.on('success', function(file, response) {
                    console.log('[Upload] Success:', response);
                    
                    if (response.success) {
                        hideProgress();
                        handleUploadSuccess(response);
                    } else {
                        showError(response.message);
                    }
                });

                this.on('error', function(file, errorMessage) {
                    console.error('[Upload] Error:', errorMessage);
                    showError(errorMessage);
                });

                this.on('uploadprogress', function(file, progress) {
                    updateProgress(progress);
                });
            }
        });
    }

    /**
     * Handle successful upload
     */
    function handleUploadSuccess(response) {
        state.uploadedImage = {
            path: response.path,
            filename: response.filename,
            width: response.width,
            height: response.height,
            dpi: response.dpi,
            format: response.format
        };

        // Show warnings if any
        if (response.warnings && response.warnings.length > 0) {
            showWarnings(response.warnings);
        }

        // Load image into canvas
        loadImageToCanvas(response.path);

        // Show editor interface
        showEditorInterface();

        // Mark as changed
        state.unsavedChanges = true;
    }

    /**
     * Initialize Fabric.js canvas editor
     */
    function initCanvasEditor() {
        var canvasEl = document.getElementById('puzzle-canvas');
        
        if (!canvasEl) {
            console.error('[PuzzleCustomizer] Canvas element not found');
            return;
        }

        // Initialize Fabric canvas
        config.canvas = new fabric.Canvas('puzzle-canvas', {
            width: 800,
            height: 600,
            backgroundColor: '#f0f0f0',
            selection: true
        });

        // Canvas events
        config.canvas.on('object:modified', function() {
            updateCanvasConfig();
            state.unsavedChanges = true;
        });

        config.canvas.on('object:scaling', function(e) {
            updateZoomDisplay();
        });

        config.canvas.on('object:rotating', function(e) {
            updateRotationDisplay();
        });

        console.log('[Canvas] Initialized');
    }

    /**
     * Load image into canvas
     */
    function loadImageToCanvas(imagePath) {
        if (!config.canvas) {
            console.error('[Canvas] Canvas not initialized');
            return;
        }

        showProgress('Caricamento immagine nell\'editor...');

        fabric.Image.fromURL(imagePath, function(img) {
            // Scale to fit canvas
            var scale = Math.min(
                config.canvas.width / img.width,
                config.canvas.height / img.height
            );

            img.set({
                left: config.canvas.width / 2,
                top: config.canvas.height / 2,
                originX: 'center',
                originY: 'center',
                scaleX: scale,
                scaleY: scale,
                selectable: true
            });

            config.canvas.clear();
            config.canvas.add(img);
            config.canvas.renderAll();

            hideProgress();
            console.log('[Canvas] Image loaded');
        }, { crossOrigin: 'anonymous' });
    }

    /**
     * Zoom controls
     */
    function setupZoomControls() {
        var zoomSlider = document.getElementById('zoom-slider');
        var zoomIn = document.getElementById('zoom-in');
        var zoomOut = document.getElementById('zoom-out');
        var zoomReset = document.getElementById('zoom-reset');

        if (zoomSlider) {
            zoomSlider.addEventListener('input', function(e) {
                var zoom = parseFloat(e.target.value);
                applyZoom(zoom);
            });
        }

        if (zoomIn) {
            zoomIn.addEventListener('click', function() {
                var currentZoom = state.canvasConfig.zoom;
                applyZoom(Math.min(currentZoom + 0.1, 3));
            });
        }

        if (zoomOut) {
            zoomOut.addEventListener('click', function() {
                var currentZoom = state.canvasConfig.zoom;
                applyZoom(Math.max(currentZoom - 0.1, 0.1));
            });
        }

        if (zoomReset) {
            zoomReset.addEventListener('click', function() {
                applyZoom(1);
            });
        }
    }

    /**
     * Apply zoom to image
     */
    function applyZoom(zoomLevel) {
        if (!config.canvas) return;

        var activeObject = config.canvas.getActiveObject();
        if (!activeObject || activeObject.type !== 'image') {
            // Get first image object
            var objects = config.canvas.getObjects();
            for (var i = 0; i < objects.length; i++) {
                if (objects[i].type === 'image') {
                    activeObject = objects[i];
                    break;
                }
            }
        }

        if (activeObject) {
            activeObject.scale(zoomLevel);
            config.canvas.renderAll();
            
            state.canvasConfig.zoom = zoomLevel;
            state.unsavedChanges = true;
            
            updateZoomDisplay();
        }
    }

    /**
     * Rotation controls
     */
    function setupRotationControls() {
        var rotateLeft = document.getElementById('rotate-left');
        var rotateRight = document.getElementById('rotate-right');
        var rotateReset = document.getElementById('rotate-reset');

        if (rotateLeft) {
            rotateLeft.addEventListener('click', function() {
                rotateImage(-90);
            });
        }

        if (rotateRight) {
            rotateRight.addEventListener('click', function() {
                rotateImage(90);
            });
        }

        if (rotateReset) {
            rotateReset.addEventListener('click', function() {
                rotateImage(0, true);
            });
        }
    }

    /**
     * Rotate image
     */
    function rotateImage(degrees, absolute) {
        if (!config.canvas) return;

        var activeObject = config.canvas.getActiveObject();
        if (!activeObject || activeObject.type !== 'image') {
            var objects = config.canvas.getObjects();
            for (var i = 0; i < objects.length; i++) {
                if (objects[i].type === 'image') {
                    activeObject = objects[i];
                    break;
                }
            }
        }

        if (activeObject) {
            if (absolute) {
                activeObject.set('angle', degrees);
            } else {
                activeObject.rotate((activeObject.angle + degrees) % 360);
            }
            
            config.canvas.renderAll();
            
            state.canvasConfig.rotation = activeObject.angle;
            state.unsavedChanges = true;
            
            updateRotationDisplay();
        }
    }

    /**
     * Crop functionality
     */
    function setupCropControls() {
        var cropButton = document.getElementById('crop-button');
        var cropApply = document.getElementById('crop-apply');
        var cropCancel = document.getElementById('crop-cancel');

        if (cropButton) {
            cropButton.addEventListener('click', function() {
                enableCropMode();
            });
        }

        if (cropApply) {
            cropApply.addEventListener('click', function() {
                applyCrop();
            });
        }

        if (cropCancel) {
            cropCancel.addEventListener('click', function() {
                cancelCrop();
            });
        }
    }

    /**
     * Enable crop mode
     */
    function enableCropMode() {
        // Add crop rectangle
        var cropRect = new fabric.Rect({
            left: 100,
            top: 100,
            width: 600,
            height: 400,
            fill: 'rgba(0,0,0,0.3)',
            stroke: '#00ff00',
            strokeWidth: 2,
            selectable: true,
            hasControls: true,
            lockRotation: true
        });

        config.canvas.add(cropRect);
        config.canvas.setActiveObject(cropRect);
        config.canvas.renderAll();

        // Show crop controls
        document.getElementById('crop-controls').style.display = 'block';
        document.getElementById('crop-button').style.display = 'none';
    }

    /**
     * Apply crop
     */
    function applyCrop() {
        var cropRect = config.canvas.getActiveObject();
        
        if (cropRect && cropRect.type === 'rect') {
            state.canvasConfig.crop = {
                x: cropRect.left,
                y: cropRect.top,
                width: cropRect.width * cropRect.scaleX,
                height: cropRect.height * cropRect.scaleY
            };

            // Remove crop rectangle
            config.canvas.remove(cropRect);
            
            // In production, you would crop the actual image here
            // For preview purposes, we just store the coordinates

            state.unsavedChanges = true;
        }

        cancelCrop();
    }

    /**
     * Cancel crop
     */
    function cancelCrop() {
        var objects = config.canvas.getObjects();
        for (var i = objects.length - 1; i >= 0; i--) {
            if (objects[i].type === 'rect' && objects[i].stroke === '#00ff00') {
                config.canvas.remove(objects[i]);
            }
        }

        config.canvas.renderAll();

        document.getElementById('crop-controls').style.display = 'none';
        document.getElementById('crop-button').style.display = 'block';
    }

    /**
     * Image filters
     */
    function setupFilterControls() {
        var filters = {
            grayscale: document.getElementById('filter-grayscale'),
            sepia: document.getElementById('filter-sepia'),
            brightness: document.getElementById('brightness-slider'),
            contrast: document.getElementById('contrast-slider'),
            saturation: document.getElementById('saturation-slider')
        };

        if (filters.grayscale) {
            filters.grayscale.addEventListener('click', function() {
                toggleFilter('grayscale');
            });
        }

        if (filters.sepia) {
            filters.sepia.addEventListener('click', function() {
                toggleFilter('sepia');
            });
        }

        if (filters.brightness) {
            filters.brightness.addEventListener('input', function(e) {
                applyFilter('brightness', parseFloat(e.target.value));
            });
        }

        if (filters.contrast) {
            filters.contrast.addEventListener('input', function(e) {
                applyFilter('contrast', parseFloat(e.target.value));
            });
        }

        if (filters.saturation) {
            filters.saturation.addEventListener('input', function(e) {
                applyFilter('saturation', parseFloat(e.target.value));
            });
        }
    }

    /**
     * Toggle filter on/off
     */
    function toggleFilter(filterType) {
        var img = getCanvasImage();
        if (!img) return;

        var filterIndex = -1;
        for (var i = 0; i < img.filters.length; i++) {
            if (img.filters[i].type === filterType) {
                filterIndex = i;
                break;
            }
        }

        if (filterIndex > -1) {
            // Remove filter
            img.filters.splice(filterIndex, 1);
        } else {
            // Add filter
            if (filterType === 'grayscale') {
                img.filters.push(new fabric.Image.filters.Grayscale());
            } else if (filterType === 'sepia') {
                img.filters.push(new fabric.Image.filters.Sepia());
            }
        }

        img.applyFilters();
        config.canvas.renderAll();

        updateCanvasConfig();
        state.unsavedChanges = true;
    }

    /**
     * Apply adjustable filter
     */
    function applyFilter(filterType, value) {
        var img = getCanvasImage();
        if (!img) return;

        // Remove existing filter of this type
        img.filters = img.filters.filter(function(f) {
            return f.type !== filterType;
        });

        // Add new filter with value
        if (filterType === 'brightness') {
            img.filters.push(new fabric.Image.filters.Brightness({ brightness: value }));
        } else if (filterType === 'contrast') {
            img.filters.push(new fabric.Image.filters.Contrast({ contrast: value }));
        } else if (filterType === 'saturation') {
            img.filters.push(new fabric.Image.filters.Saturation({ saturation: value }));
        }

        img.applyFilters();
        config.canvas.renderAll();

        updateCanvasConfig();
        state.unsavedChanges = true;
    }

    /**
     * Get canvas image object
     */
    function getCanvasImage() {
        var objects = config.canvas.getObjects();
        for (var i = 0; i < objects.length; i++) {
            if (objects[i].type === 'image') {
                return objects[i];
            }
        }
        return null;
    }

    /**
     * Text customization
     */
    function setupTextControls() {
        var addTextBtn = document.getElementById('add-text');
        var textInput = document.getElementById('custom-text-input');
        var fontSelect = document.getElementById('font-select');
        var fontSizeInput = document.getElementById('font-size-input');
        var textColorPicker = document.getElementById('text-color-picker');

        if (addTextBtn && textInput) {
            addTextBtn.addEventListener('click', function() {
                var text = textInput.value || 'Il tuo testo';
                addTextToCanvas(text);
            });
        }

        if (textInput) {
            textInput.addEventListener('input', function() {
                state.customText = this.value;
                state.unsavedChanges = true;
            });
        }

        if (fontSelect) {
            fontSelect.addEventListener('change', function() {
                state.selectedOptions.font = this.value;
                updateActiveText();
                state.unsavedChanges = true;
            });
        }

        if (fontSizeInput) {
            fontSizeInput.addEventListener('input', function() {
                updateActiveText();
                state.unsavedChanges = true;
            });
        }

        // Initialize color picker (Spectrum.js)
        if (textColorPicker) {
            if (config.enableRGBSelector) {
                // Full RGB selector
                $(textColorPicker).spectrum({
                    type: "color",
                    showInput: true,
                    showAlpha: false,
                    preferredFormat: "hex",
                    change: function(color) {
                        state.selectedOptions.text_color = color.toHexString();
                        updateActiveText();
                        state.unsavedChanges = true;
                    }
                });
            } else {
                // Predefined palette only
                var palette = window.PUZZLE_TEXT_COLORS || [];
                $(textColorPicker).spectrum({
                    showPaletteOnly: true,
                    showPalette: true,
                    palette: palette,
                    change: function(color) {
                        state.selectedOptions.text_color = color.toHexString();
                        updateActiveText();
                        state.unsavedChanges = true;
                    }
                });
            }
        }
    }

    /**
     * Add text to canvas
     */
    function addTextToCanvas(textContent) {
        if (!config.canvas) return;

        var text = new fabric.Text(textContent, {
            left: config.canvas.width / 2,
            top: config.canvas.height - 100,
            originX: 'center',
            originY: 'center',
            fontFamily: state.selectedOptions.font || 'Arial',
            fontSize: parseInt(document.getElementById('font-size-input').value) || 48,
            fill: state.selectedOptions.text_color || '#000000',
            selectable: true,
            editable: true
        });

        config.canvas.add(text);
        config.canvas.setActiveObject(text);
        config.canvas.renderAll();

        state.unsavedChanges = true;
    }

    /**
     * Update active text properties
     */
    function updateActiveText() {
        if (!config.canvas) return;

        var activeObject = config.canvas.getActiveObject();
        if (activeObject && activeObject.type === 'text') {
            if (state.selectedOptions.font) {
                activeObject.set('fontFamily', state.selectedOptions.font);
            }

            var fontSize = parseInt(document.getElementById('font-size-input').value);
            if (fontSize) {
                activeObject.set('fontSize', fontSize);
            }

            if (state.selectedOptions.text_color) {
                activeObject.set('fill', state.selectedOptions.text_color);
            }

            config.canvas.renderAll();
        }
    }

    /**
     * Initialize options selectors
     */
    function initOptionsSelectors() {
        // Puzzle options (pieces/dimensions)
        var optionRadios = document.querySelectorAll('input[name="puzzle_option"]');
        optionRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                state.selectedOptions.puzzle_option = this.value;
                updatePriceSummary();
                state.unsavedChanges = true;
            });
        });

        // Box color
        var colorRadios = document.querySelectorAll('input[name="box_color"]');
        colorRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                state.selectedOptions.box_color = this.value;
                updatePriceSummary();
                update3DPreview();
                state.unsavedChanges = true;
            });
        });
    }

    /**
     * Update price summary
     */
    function updatePriceSummary() {
        var basePrice = parseFloat(window.PUZZLE_BASE_PRICE) || 0;
        var totalPrice = basePrice;

        // Add option price impact
        if (state.selectedOptions.puzzle_option) {
            var optionPrice = parseFloat(
                document.querySelector('input[name="puzzle_option"][value="' + state.selectedOptions.puzzle_option + '"]')
                    .getAttribute('data-price-impact')
            ) || 0;
            totalPrice += optionPrice;
        }

        // Add box color price impact
        if (state.selectedOptions.box_color) {
            var colorPrice = parseFloat(
                document.querySelector('input[name="box_color"][value="' + state.selectedOptions.box_color + '"]')
                    .getAttribute('data-price-impact')
            ) || 0;
            totalPrice += colorPrice;
        }

        // Update display
        var priceElement = document.getElementById('total-price');
        if (priceElement) {
            priceElement.textContent = totalPrice.toFixed(2) + ' €';
        }
    }

    /**
     * Initialize 3D preview (Three.js)
     */
    function initPreview3D() {
        if (typeof THREE === 'undefined') {
            console.warn('[3D Preview] Three.js not loaded');
            return;
        }

        // This would initialize a Three.js scene with a 3D box model
        // Implementation depends on your specific box 3D model
        
        console.log('[3D Preview] Initialized');
    }

    /**
     * Update 3D preview with current configuration
     */
    function update3DPreview() {
        if (!config.enable3DPreview) return;

        // Update 3D box model with selected color and image
        // This would update textures on the Three.js 3D model

        console.log('[3D Preview] Updated');
    }

    /**
     * Update canvas configuration state
     */
    function updateCanvasConfig() {
        var img = getCanvasImage();
        if (!img) return;

        state.canvasConfig = {
            zoom: img.scaleX,
            rotation: img.angle,
            crop: state.canvasConfig.crop, // Preserve crop settings
            filters: img.filters.map(function(f) {
                return {
                    type: f.type,
                    value: f.brightness || f.contrast || f.saturation || null
                };
            })
        };
    }

    /**
     * Auto-save functionality
     */
    function initAutoSave() {
        setInterval(function() {
            if (state.unsavedChanges) {
                saveConfiguration(false); // Silent save
            }
        }, config.autoSaveInterval);
    }

    /**
     * Save configuration
     */
    function saveConfiguration(showMessage) {
        if (showMessage !== false) {
            showMessage = true;
        }

        if (showMessage) {
            showProgress('Salvataggio in corso...');
        }

        // Prepare data
        var data = {
            id_customer: window.PUZZLE_CUSTOMER_ID,
            id_cart: window.PUZZLE_CART_ID,
            id_product: window.PUZZLE_PRODUCT_ID,
            uploaded_image: state.uploadedImage,
            selected_options: state.selectedOptions,
            custom_text: state.customText,
            canvas_config: state.canvasConfig
        };

        // Send AJAX request
        fetch(config.saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                state.unsavedChanges = false;
                
                if (showMessage) {
                    hideProgress();
                    showSuccess('Configurazione salvata con successo');
                }
            } else {
                if (showMessage) {
                    showError(data.message || 'Errore nel salvataggio');
                }
            }
        })
        .catch(error => {
            console.error('[Save] Error:', error);
            if (showMessage) {
                showError('Errore di connessione');
            }
        });
    }

    /**
     * Add to cart
     */
    function addToCart() {
        // Validate required fields
        if (!state.uploadedImage) {
            showError('Devi caricare un\'immagine');
            return;
        }

        if (!state.selectedOptions.puzzle_option) {
            showError('Seleziona dimensioni e numero di pezzi');
            return;
        }

        if (!state.selectedOptions.box_color) {
            showError('Seleziona il colore della scatola');
            return;
        }

        // Save configuration first
        showProgress('Aggiunta al carrello...');

        saveConfiguration(false);

        // Add to cart via AJAX
        var formData = new FormData();
        formData.append('action', 'add_to_cart');
        formData.append('id_product', window.PUZZLE_PRODUCT_ID);
        formData.append('id_customization', 1); // This would be from save response
        formData.append('qty', 1);

        fetch('/cart', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideProgress();
            
            if (data.success) {
                showSuccess('Prodotto aggiunto al carrello!');
                
                // Redirect to cart after 2 seconds
                setTimeout(function() {
                    window.location.href = '/cart';
                }, 2000);
            } else {
                showError(data.message || 'Errore nell\'aggiunta al carrello');
            }
        })
        .catch(error => {
            hideProgress();
            console.error('[Cart] Error:', error);
            showError('Errore di connessione');
        });
    }

    /**
     * UI Helper functions
     */
    function showEditorInterface() {
        document.getElementById('upload-section').style.display = 'none';
        document.getElementById('editor-section').style.display = 'block';
    }

    function showProgress(message) {
        var progressEl = document.getElementById('progress-overlay');
        if (progressEl) {
            progressEl.querySelector('.progress-message').textContent = message;
            progressEl.style.display = 'flex';
        }
    }

    function hideProgress() {
        var progressEl = document.getElementById('progress-overlay');
        if (progressEl) {
            progressEl.style.display = 'none';
        }
    }

    function updateProgress(percent) {
        var progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = percent + '%';
            progressBar.textContent = Math.round(percent) + '%';
        }
    }

    function showError(message) {
        hideProgress();
        alert('Errore: ' + message);
        // In production, use a proper notification system
    }

    function showWarnings(warnings) {
        var warningEl = document.getElementById('warnings-container');
        if (warningEl) {
            var html = '<div class="alert alert-warning"><ul>';
            warnings.forEach(function(warning) {
                html += '<li>' + warning + '</li>';
            });
            html += '</ul></div>';
            warningEl.innerHTML = html;
            warningEl.style.display = 'block';
        }
    }

    function showSuccess(message) {
        hideProgress();
        alert('Successo: ' + message);
        // In production, use a proper notification system
    }

    function updateZoomDisplay() {
        var display = document.getElementById('zoom-display');
        if (display) {
            display.textContent = Math.round(state.canvasConfig.zoom * 100) + '%';
        }

        var slider = document.getElementById('zoom-slider');
        if (slider) {
            slider.value = state.canvasConfig.zoom;
        }
    }

    function updateRotationDisplay() {
        var display = document.getElementById('rotation-display');
        if (display) {
            display.textContent = Math.round(state.canvasConfig.rotation) + '°';
        }
    }

    /**
     * Attach event listeners
     */
    function attachEventListeners() {
        // Save button
        var saveBtn = document.getElementById('save-configuration');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                saveConfiguration(true);
            });
        }

        // Add to cart button
        var addToCartBtn = document.getElementById('add-to-cart-button');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                addToCart();
            });
        }

        // Setup all control panels
        setupZoomControls();
        setupRotationControls();
        setupCropControls();
        setupFilterControls();
        setupTextControls();

        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (state.unsavedChanges) {
                e.preventDefault();
                e.returnValue = 'Hai modifiche non salvate. Sei sicuro di voler uscire?';
                return e.returnValue;
            }
        });
    }

    // Public API
    return {
        init: init,
        saveConfiguration: saveConfiguration,
        addToCart: addToCart
    };
})();

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    PuzzleCustomizer.init();
});
```

---

## 6. Development Guidelines

### 6.1 Code Standards

**PHP:**
- Follow PrestaShop coding standards
- Use ObjectModel for database entities
- Implement proper validation and sanitization
- Use Db::getInstance()->execute() for queries
- Always use pSQL() for string escaping
- Implement proper error handling with try-catch

**JavaScript:**
- Use ES5 for IE11 compatibility or ES6+ with Babel transpilation
- Follow modular pattern for organization
- Implement proper event delegation
- Use strict mode
- Add comprehensive error handling

**CSS:**
- Follow BEM naming convention
- Mobile-first responsive design
- Use CSS variables for theming
- Optimize for performance (minimize reflows)

### 6.2 Security Checklist

- ✅ CSRF token validation on all forms
- ✅ SQL injection prevention (pSQL, prepared statements)
- ✅ XSS prevention (htmlspecialchars, escape on output)
- ✅ File upload validation (MIME type, size, extension)
- ✅ Antivirus scanning for uploads
- ✅ Path traversal prevention
- ✅ Access control (admin vs customer)
- ✅ Rate limiting on uploads
- ✅ Secure session management

### 6.3 Performance Optimization

- ✅ Lazy load images
- ✅ Minify CSS/JS
- ✅ Use CDN for libraries
- ✅ Implement caching (OPcache, Redis)
- ✅ Optimize database queries (proper indexes)
- ✅ Asynchronous image processing for large files
- ✅ Progressive image loading
- ✅ Database query optimization

### 6.4 Testing Requirements

**Unit Tests:**
- Test all model classes (CRUD operations)
- Test ImageProcessor methods
- Test validation functions

**Integration Tests:**
- Test upload workflow
- Test cart integration
- Test order creation
- Test production file generation

**UI Tests:**
- Test customizer interface on all browsers
- Test mobile responsiveness
- Test drag-and-drop upload
- Test canvas operations

**Security Tests:**
- Test file upload restrictions
- Test SQL injection attempts
- Test XSS attempts
- Test CSRF protection

---

## 7. Installation Instructions

### 7.1 Server Requirements

**Minimum:**
- PHP 7.3.33
- MySQL 5.7+
- Apache 2.4+ or Nginx 1.18+
- ImageMagick or GD extension
- Memory: 512 MB minimum, 2 GB recommended
- Disk space: 10 GB minimum for uploads

**PHP Extensions Required:**
- mysqli
- gd OR imagick
- fileinfo
- zip
- exif (optional, for EXIF data)
- curl

**PHP Configuration:**
```ini
upload_max_filesize = 50M
post_max_size = 52M
memory_limit = 512M
max_execution_time = 300
max_input_time = 300
```

### 7.2 Installation Steps

1. **Upload module files**
   ```bash
   # Upload to PrestaShop modules directory
   /modules/puzzlecustomizer/
   ```

2. **Set permissions**
   ```bash
   chmod 755 /modules/puzzlecustomizer/
   chmod 777 /modules/puzzlecustomizer/uploads/
   chmod 777 /modules/puzzlecustomizer/fonts/
   ```

3. **Install module via BackOffice**
   - Go to Modules > Module Manager
   - Search for "Puzzle Customizer"
   - Click Install

4. **Configure module**
   - Go to Modules > Puzzle Customizer > Configuration
   - Set global options
   - Add image formats
   - Add fonts
   - Add box colors
   - Add text colors

5. **Enable for products**
   - Go to Modules > Puzzle Customizer > Products
   - Select products to enable customization
   - Configure per-product settings

### 7.3 Post-Installation

**Test the installation:**
1. Visit a customizable product page
2. Click customization button
3. Upload a test image
4. Configure options
5. Add to cart
6. Complete test order
7. Download production files from admin

---

## 8. API Documentation

### 8.1 Frontend AJAX Endpoints

**Upload Image**
```
POST /modules/puzzlecustomizer/controllers/front/Upload.php

Parameters:
- file: Image file (multipart/form-data)
- id_customer: Customer ID
- id_cart: Cart ID
- id_product: Product ID

Response:
{
  "success": true,
  "path": "/uploads/temp/puzzle_123_456_1634567890_abc123.jpg",
  "filename": "original_filename.jpg",
  "width": 4000,
  "height": 3000,
  "dpi": 300,
  "format": "JPEG",
  "warnings": ["DPI basso (200). Consigliato: 300"]
}
```

**Save Configuration**
```
POST /modules/puzzlecustomizer/controllers/front/SaveConfig.php

Body (JSON):
{
  "id_customer": 123,
  "id_cart": 456,
  "id_product": 789,
  "uploaded_image": {
    "path": "...",
    "filename": "...",
    "width": 4000,
    "height": 3000
  },
  "selected_options": {
    "puzzle_option": 5,
    "box_color": 2,
    "text_color": 1,
    "font": 3
  },
  "custom_text": "Il mio puzzle personalizzato",
  "canvas_config": {
    "zoom": 1.2,
    "rotation": 90,
    "crop": {"x": 100, "y": 100, "width": 800, "height": 600},
    "filters": [{"type": "brightness", "value": 0.1}]
  }
}

Response:
{
  "success": true,
  "id_customization": 123,
  "message": "Configurazione salvata"
}
```

**Generate 3D Preview**
```
POST /modules/puzzlecustomizer/controllers/front/Preview.php

Body (JSON):
{
  "id_customization": 123,
  "box_color": 2
}

Response:
{
  "success": true,
  "preview_url": "/modules/puzzlecustomizer/previews/preview_123.png"
}
```

---

## 9. Maintenance and Support

### 9.1 Regular Maintenance Tasks

**Daily:**
- Monitor upload directory size
- Check error logs
- Verify disk space

**Weekly:**
- Clean temporary files older than 7 days
- Review failed uploads
- Check database size

**Monthly:**
- Update external libraries (Fabric.js, Dropzone.js, etc.)
- Review performance metrics
- Backup customization database tables

### 9.2 Troubleshooting Guide

**Upload fails:**
1. Check PHP upload limits
2. Verify directory permissions
3. Check available disk space
4. Review error logs

**Canvas not loading:**
1. Check browser console for JavaScript errors
2. Verify Fabric.js is loaded
3. Check CORS settings
4. Test with different browsers

**Production files not generating:**
1. Check ImageMagick installation
2. Verify file permissions
3. Check memory limits
4. Review error logs

**Performance issues:**
1. Enable OPcache
2. Optimize database queries
3. Implement Redis caching
4. Use CDN for static assets

---

## 10. Development Timeline

### Phase 1: Core Setup (Week 1)
- ✅ Database schema implementation
- ✅ Main module file
- ✅ Model classes (PuzzleProduct, PuzzleOption, etc.)
- ✅ Admin tab structure

### Phase 2: Admin Backend (Week 2-3)
- ✅ Product management interface
- ✅ Options management (dimensions, colors, fonts)
- ✅ Image format management
- ✅ Color pickers integration
- ✅ Font upload system

### Phase 3: Frontend Upload (Week 4)
- ✅ Dropzone.js integration
- ✅ File validation
- ✅ Image format detection
- ✅ Progress indicators
- ✅ Error handling

### Phase 4: Canvas Editor (Week 5-6)
- ✅ Fabric.js integration
- ✅ Zoom/pan/rotate controls
- ✅ Crop functionality
- ✅ Image filters
- ✅ Text overlay

### Phase 5: Options & Preview (Week 7)
- ✅ Option selectors (radio buttons, color swatches)
- ✅ Price calculator
- ✅ 3D preview (optional)
- ✅ Real-time updates

### Phase 6: Image Processing (Week 8)
- ✅ ImageProcessor class
- ✅ Format conversions
- ✅ DPI optimization
- ✅ Production file generation

### Phase 7: Cart Integration (Week 9)
- ✅ Add to cart functionality
- ✅ Cart customization display
- ✅ Order creation hooks
- ✅ Customization data persistence

### Phase 8: Admin Orders (Week 10)
- ✅ Customized orders list
- ✅ Preview interface
- ✅ Production file download
- ✅ ZIP generation

### Phase 9: Testing & QA (Week 11-12)
- ✅ Unit tests
- ✅ Integration tests
- ✅ Browser compatibility testing
- ✅ Security audit
- ✅ Performance optimization

### Phase 10: Documentation & Deployment (Week 13)
- ✅ User documentation
- ✅ Admin guide
- ✅ API documentation
- ✅ Final deployment

**Total estimated time: 13 weeks (3 months)**

---

## 11. Important Notes for Developers

### Critical Requirements

1. **ALL UI text must be in Italian**
   - Admin interface: Italian
   - Frontend interface: Italian
   - Error messages: Italian
   - Validation messages: Italian

2. **No hardcoded limits**
   - Admin can add unlimited options
   - Admin can add unlimited colors
   - Admin can add unlimited fonts
   - Admin can configure all formats

3. **Security is paramount**
   - Never trust user input
   - Always validate file uploads
   - Sanitize all database inputs
   - Use CSRF tokens

4. **Performance optimization**
   - Large file handling must be asynchronous
   - Database queries must be optimized
   - Frontend should be responsive even during processing

5. **Production file quality**
   - Generated files must be 300 DPI TIFF
   - Color profiles must be preserved
   - No quality loss in transformations

---

## 12. External Libraries Required

Download and include these libraries in `/modules/puzzlecustomizer/libs/`:

1. **Fabric.js** v5.3.0
   - Source: http://fabricjs.com/
   - File: fabric.min.js

2. **Dropzone.js** v5.9.3
   - Source: https://www.dropzonejs.com/
   - Files: dropzone.min.js, dropzone.min.css

3. **Cropper.js** v1.5.13
   - Source: https://fengyuanchen.github.io/cropperjs/
   - Files: cropper.min.js, cropper.min.css

4. **Spectrum** v1.8.1 (Color Picker)
   - Source: https://bgrins.github.io/spectrum/
   - Files: spectrum.min.js, spectrum.min.css

5. **Three.js** r140 (Optional, for 3D preview)
   - Source: https://threejs.org/
   - File: three.min.js

---

## 13. Summary

This complete specification document provides everything needed to develop the PrestaShop Puzzle Customizer plugin from scratch. The plugin will:

✅ Replicate and improve fotoartpuzzle.it functionality  
✅ Provide 100% dynamic admin backend  
✅ Support unlimited options, colors, fonts, and formats  
✅ Handle high-resolution images up to 50MB  
✅ Generate production-ready files automatically  
✅ Integrate seamlessly with PrestaShop 1.7.6.9  
✅ Maintain highest security standards  
✅ Deliver professional user experience  

**All UI text in Italian as required.**

---

**Document End**

For questions or clarifications during development, refer to specific sections above or consult the official PrestaShop documentation at https://devdocs.prestashop.com/
