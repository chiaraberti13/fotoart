private function generateAllMissingFiles() 
    {
        echo "<h2>📁 Generazione File Mancanti</h2>";
        
        $this->generateUploadController();
        $this->generatePreviewController();
        $this->generateCustomizerController();
        $this->generateJavaScriptFiles();
        $this->generateTemplateFiles();
        $this->generateCSSFiles();
        $this->generateImageProcessor();
        $this->generateHtaccessFiles();
        
        echo "<div class='success'>✅ Generazione file completata</div>";
    }
    
    private function generateUploadController() 
    {
        $controller_path = $this->module_path . '/controllers/front/UploadController.php';
        
        if (file_exists($controller_path)) {
            echo "<div class='info'>ℹ️ UploadController.php già esistente</div>";
            return;
        }
        
        $controller_content = '<?php

class Art_puzzleUploadModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    
    public function __construct()
    {
        parent::__construct();
        $this->display_header = false;
        $this->display_footer = false;
    }
    
    public function postProcess()
    {
        if (!$this->isXmlHttpRequest()) {
            $this->ajaxRender(json_encode([
                \'success\' => false,
                \'error\' => \'Richiesta non valida\'
            ]));
            return;
        }
        
        try {
            if (isset($_FILES[\'puzzle_image\']) && $_FILES[\'puzzle_image\'][\'error\'] === UPLOAD_ERR_OK) {
                $result = $this->handleImageUpload($_FILES[\'puzzle_image\']);
                $this->ajaxRender(json_encode($result));
            } else {
                $this->ajaxRender(json_encode([
                    \'success\' => false,
                    \'error\' => \'Nessun file ricevuto: \' . $this->getUploadError()
                ]));
            }
        } catch (Exception $e) {
            $this->ajaxRender(json_encode([
                \'success\' => false,
                \'error\' => $e->getMessage()
            ]));
        }
    }
    
    private function getUploadError() 
    {
        if (!isset($_FILES[\'puzzle_image\'])) {
            return \'Campo file non trovato\';
        }
        
        $error_codes = [
            UPLOAD_ERR_INI_SIZE => \'File troppo grande (limite server)\',
            UPLOAD_ERR_FORM_SIZE => \'File troppo grande (limite form)\',
            UPLOAD_ERR_PARTIAL => \'Upload parziale\',
            UPLOAD_ERR_NO_FILE => \'Nessun file selezionato\',
            UPLOAD_ERR_NO_TMP_DIR => \'Cartella temporanea mancante\',
            UPLOAD_ERR_CANT_WRITE => \'Impossibile scrivere su disco\',
            UPLOAD_ERR_EXTENSION => \'Upload bloccato da estensione\'
        ];
        
        $error_code = $_FILES[\'puzzle_image\'][\'error\'];
        return isset($error_codes[$error_code]) ? $error_codes[$error_code] : \'Errore sconosciuto\';
    }
    
    private function handleImageUpload($file)
    {
        // Validazione tipo file
        $allowed_types = [\'image/jpeg\', \'image/jpg\', \'image/png\', \'image/gif\'];
        $file_type = mime_content_type($file[\'tmp_name\']);
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception(\'Formato non supportato. Usa JPG, PNG o GIF.\');
        }
        
        // Controllo dimensioni (max 10MB)
        if ($file[\'size\'] > 10485760) {
            throw new Exception(\'File troppo grande. Massimo 10MB.\');
        }
        
        // Verifica che sia realmente un\'immagine
        $image_info = getimagesize($file[\'tmp_name\']);
        if (!$image_info) {
            throw new Exception(\'File non è un\'immagine valida.\');
        }
        
        // Crea cartella upload se non esiste
        $upload_dir = _PS_IMG_DIR_ . \'art_puzzle/uploads/\';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception(\'Impossibile creare cartella upload.\');
            }
        }
        
        // Genera nome file sicuro
        $file_extension = strtolower(pathinfo($file[\'name\'], PATHINFO_EXTENSION));
        $new_filename = \'puzzle_\' . uniqid() . \'_\' . date(\'YmdHis\') . \'.\' . $file_extension;
        $destination = $upload_dir . $new_filename;
        
        // Sposta file uploadato
        if (!move_uploaded_file($file[\'tmp_name\'], $destination)) {
            throw new Exception(\'Errore durante il salvataggio del file.\');
        }
        
        // Ottimizza immagine se necessario
        $this->optimizeImage($destination, $image_info);
        
        // Log upload
        $this->logUpload($new_filename, $file[\'size\'], $image_info);
        
        return [
            \'success\' => true,
            \'filename\' => $new_filename,
            \'url\' => $this->context->shop->getBaseURL() . \'img/art_puzzle/uploads/\' . $new_filename,
            \'path\' => $destination,
            \'width\' => $image_info[0],
            \'height\' => $image_info[1],
            \'size\' => $file[\'size\']
        ];
    }
    
    private function optimizeImage($image_path, $image_info) 
    {
        // Ridimensiona se troppo grande
        $max_width = 2000;
        $max_height = 2000;
        
        if ($image_info[0] > $max_width || $image_info[1] > $max_height) {
            $this->resizeImage($image_path, $max_width, $max_height, $image_info);
        }
    }
    
    private function resizeImage($image_path, $max_width, $max_height, $image_info) 
    {
        $ratio = min($max_width / $image_info[0], $max_height / $image_info[1]);
        $new_width = intval($image_info[0] * $ratio);
        $new_height = intval($image_info[1] * $ratio);
        
        $new_image = imagecreatetruecolor($new_width, $new_height);
        
        switch ($image_info[2]) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($image_path);
                imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $image_info[0], $image_info[1]);
                imagejpeg($new_image, $image_path, 90);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($image_path);
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);<?php
/**
 * Art Puzzle Debug & Auto-Fix Tool - VERSIONE AVANZATA
 * Analizza e corregge automaticamente tutti i problemi del modulo art_puzzle
 * Focus specifico sui problemi di upload immagini
 * 
 * ISTRUZIONI:
 * 1. Carica questo file nella cartella principale del modulo art_puzzle
 * 2. Esegui da browser: http://tuodominio.com/modules/art_puzzle/art_puzzle_debug_fixer.php
 * 3. Il tool analizzerà e correggerà automaticamente tutti i problemi
 * 4. Usa i parametri GET per azioni specifiche:
 *    - ?action=fix -> Applica tutte le correzioni automaticamente
 *    - ?action=test_upload -> Testa il sistema di upload
 *    - ?action=generate_missing -> Genera tutti i file mancanti
 *    - ?action=reset_permissions -> Resetta tutti i permessi
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minuti max

class ArtPuzzleDebugFixer 
{
    private $module_path;
    private $prestashop_root;
    private $errors = [];
    private $fixes = [];
    private $warnings = [];
    private $success_fixes = [];
    private $action;
    private $log_file;
    
    public function __construct() 
    {
        $this->module_path = dirname(__FILE__);
        $this->prestashop_root = dirname(dirname(dirname(__FILE__)));
        $this->action = isset($_GET['action']) ? $_GET['action'] : 'diagnose';
        $this->log_file = $this->module_path . '/debug_log_' . date('Y-m-d_H-i-s') . '.txt';
        
        // Verifica che siamo nel percorso corretto
        if (!file_exists($this->prestashop_root . '/config/settings.inc.php')) {
            die('❌ Errore: Impossibile individuare la root di PrestaShop');
        }
        
        // Include PrestaShop
        require_once($this->prestashop_root . '/config/config.inc.php');
        
        // Inizia logging
        $this->logMessage("=== ART PUZZLE DEBUG SESSION STARTED ===");
        $this->logMessage("Action: " . $this->action);
        $this->logMessage("PrestaShop Version: " . _PS_VERSION_);
        $this->logMessage("PHP Version: " . PHP_VERSION);
    }
    
    public function runDiagnostic() 
    {
        $this->renderHeader();
        
        switch ($this->action) {
            case 'fix':
                $this->runFullDiagnosticAndFix();
                break;
            case 'test_upload':
                $this->testUploadSystem();
                break;
            case 'generate_missing':
                $this->generateAllMissingFiles();
                break;
            case 'reset_permissions':
                $this->resetAllPermissions();
                break;
            case 'check_ajax':
                $this->checkAjaxConnectivity();
                break;
            case 'analyze_js':
                $this->analyzeJavaScriptErrors();
                break;
            default:
                $this->runFullDiagnostic();
                break;
        }
        
        $this->renderFooter();
    }
    
    private function renderHeader() 
    {
        echo "<!DOCTYPE html><html><head>";
        echo "<title>Art Puzzle Debug Tool</title>";
        echo "<style>
            body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #007cba; border-bottom: 3px solid #007cba; padding-bottom: 10px; }
            h2 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 30px; }
            h3 { color: #555; }
            .success { color: #28a745; font-weight: bold; }
            .error { color: #dc3545; font-weight: bold; }
            .warning { color: #ffc107; font-weight: bold; }
            .info { color: #17a2b8; font-weight: bold; }
            .code { background: #f8f9fa; padding: 10px; border-left: 4px solid #007cba; margin: 10px 0; font-family: monospace; }
            .action-buttons { margin: 20px 0; }
            .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
            .btn-primary { background: #007cba; color: white; }
            .btn-success { background: #28a745; color: white; }
            .btn-warning { background: #ffc107; color: black; }
            .btn-danger { background: #dc3545; color: white; }
            .progress-bar { width: 100%; background: #f0f0f0; border-radius: 10px; margin: 10px 0; }
            .progress { height: 20px; background: #007cba; border-radius: 10px; transition: width 0.3s; }
            .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
            .status-card { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #007cba; }
        </style>";
        echo "</head><body><div class='container'>";
        echo "<h1>🔧 Art Puzzle - Diagnostic & Auto-Fix Tool AVANZATO</h1>";
        
        // Pulsanti azioni rapide
        echo "<div class='action-buttons'>";
        echo "<a href='?action=fix' class='btn btn-success'>🚀 Auto-Fix Completo</a>";
        echo "<a href='?action=test_upload' class='btn btn-primary'>🧪 Test Upload</a>";
        echo "<a href='?action=generate_missing' class='btn btn-warning'>📁 Genera File Mancanti</a>";
        echo "<a href='?action=reset_permissions' class='btn btn-danger'>🔐 Reset Permessi</a>";
        echo "<a href='?action=check_ajax' class='btn btn-info'>📡 Test AJAX</a>";
        echo "<a href='?' class='btn btn-primary'>📊 Diagnosi Completa</a>";
        echo "</div><hr>";
    }
    
    private function runFullDiagnostic() 
    {
        $this->logMessage("Starting full diagnostic");
        
        $this->checkModuleStructure();
        $this->checkHooks();
        $this->checkControllers();
        $this->checkJavaScriptFiles();
        $this->checkImageUploadPaths();
        $this->checkTemplates();
        $this->checkPermissions();
        $this->checkDatabaseTables();
        $this->checkServerConfiguration();
        $this->checkModuleConfiguration();
        $this->analyzeCSSFiles();
        $this->checkPSCompatibility();
        
        $this->displayResults();
        $this->suggestActions();
    }
    
    private function runFullDiagnosticAndFix() 
    {
        echo "<h2>🚀 Esecuzione Auto-Fix Completo</h2>";
        
        $this->runFullDiagnostic();
        $this->autoFixProblems();
        $this->generateAllMissingFiles();
        $this->resetAllPermissions();
        $this->testUploadSystem();
        
        echo "<div class='success' style='margin: 20px 0; padding: 15px; background: #d4edda; border-radius: 5px;'>";
        echo "✅ <strong>Auto-Fix Completo Terminato!</strong><br>";
        echo "📝 Controlla il file di log: " . basename($this->log_file) . "<br>";
        echo "🔄 Prova ora il caricamento immagini dal frontend";
        echo "</div>";
    }
    
    private function checkModuleStructure() 
    {
        echo "<h2>📁 Controllo Struttura Modulo</h2>";
        
        $required_files = [
            'art_puzzle.php',
            'config.xml',
            'controllers/front/UploadController.php',
            'controllers/front/PreviewController.php',
            'controllers/front/CustomizerController.php',
            'views/js/front.js',
            'views/js/preview-generator.js',
            'views/js/cropper-integration.js',
            'views/css/front.css',
            'classes/PuzzleImageProcessor.php'
        ];
        
        foreach ($required_files as $file) {
            $file_path = $this->module_path . '/' . $file;
            if (file_exists($file_path)) {
                echo "✅ $file - OK<br>";
            } else {
                echo "❌ $file - MANCANTE<br>";
                $this->errors[] = "File mancante: $file";
            }
        }
        
        // Controlla se esiste il file ajax.php obsoleto
        if (file_exists($this->module_path . '/ajax.php')) {
            $this->warnings[] = "File ajax.php obsoleto presente - dovrebbe essere rimosso";
            echo "⚠️ ajax.php - OBSOLETO (va rimosso)<br>";
        }
    }
    
    private function checkHooks() 
    {
        echo "<h2>🎣 Controllo Hooks</h2>";
        
        $main_file = $this->module_path . '/art_puzzle.php';
        if (!file_exists($main_file)) {
            $this->errors[] = "File principale art_puzzle.php non trovato";
            return;
        }
        
        $content = file_get_contents($main_file);
        
        $required_hooks = [
            'displayHeader',
            'displayProductButtons', 
            'displayProductExtraContent',
            'actionCartSave'
        ];
        
        foreach ($required_hooks as $hook) {
            if (strpos($content, "hook$hook") !== false) {
                echo "✅ Hook $hook - REGISTRATO<br>";
            } else {
                echo "❌ Hook $hook - NON REGISTRATO<br>";
                $this->errors[] = "Hook mancante: $hook";
            }
        }
        
        // Verifica installazione hooks
        $this->checkHookInstallation();
    }
    
    private function checkHookInstallation() 
    {
        try {
            $sql = "SELECT h.name FROM " . _DB_PREFIX_ . "hook h 
                   INNER JOIN " . _DB_PREFIX_ . "hook_module hm ON h.id_hook = hm.id_hook 
                   INNER JOIN " . _DB_PREFIX_ . "module m ON hm.id_module = m.id_module 
                   WHERE m.name = 'art_puzzle'";
            
            $hooks = Db::getInstance()->executeS($sql);
            
            if ($hooks) {
                echo "📌 Hooks installati nel database:<br>";
                foreach ($hooks as $hook) {
                    echo "&nbsp;&nbsp;• " . $hook['name'] . "<br>";
                }
            } else {
                $this->errors[] = "Nessun hook installato nel database";
            }
        } catch (Exception $e) {
            $this->errors[] = "Errore controllo hooks database: " . $e->getMessage();
        }
    }
    
    private function checkControllers() 
    {
        echo "<h2>🎮 Controllo Controllers</h2>";
        
        $controllers = [
            'UploadController.php',
            'PreviewController.php', 
            'CustomizerController.php',
            'CartController.php'
        ];
        
        foreach ($controllers as $controller) {
            $controller_path = $this->module_path . '/controllers/front/' . $controller;
            
            if (file_exists($controller_path)) {
                $content = file_get_contents($controller_path);
                
                // Verifica struttura base controller
                if (strpos($content, 'class') !== false && strpos($content, 'ModuleFrontController') !== false) {
                    echo "✅ $controller - STRUTTURA OK<br>";
                    
                    // Controlli specifici per UploadController
                    if ($controller === 'UploadController.php') {
                        $this->checkUploadController($content);
                    }
                    
                } else {
                    echo "❌ $controller - STRUTTURA ERRATA<br>";
                    $this->errors[] = "Controller $controller ha struttura errata";
                }
            } else {
                echo "❌ $controller - NON TROVATO<br>";
                $this->errors[] = "Controller mancante: $controller";
            }
        }
    }
    
    private function testUploadSystem() 
    {
        echo "<h2>🧪 Test Sistema Upload Avanzato</h2>";
        
        // Test 1: Verifica configurazione PHP
        echo "<h3>1. Configurazione PHP</h3>";
        $php_checks = [
            'file_uploads' => ini_get('file_uploads'),
            'upload_max_filesize' => ini_get('upload_max_filesize'), 
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_input_vars' => ini_get('max_input_vars')
        ];
        
        foreach ($php_checks as $setting => $value) {
            echo "<div class='code'>$setting: <strong>$value</strong></div>";
        }
        
        // Test 2: Verifica cartelle upload
        echo "<h3>2. Test Cartelle Upload</h3>";
        $upload_paths = [
            _PS_IMG_DIR_ . 'art_puzzle/',
            _PS_IMG_DIR_ . 'art_puzzle/uploads/',
            _PS_IMG_DIR_ . 'art_puzzle/temp/',
            $this->module_path . '/uploads/',
            $this->module_path . '/img/'
        ];
        
        foreach ($upload_paths as $path) {
            $this->testDirectory($path);
        }
        
        // Test 3: Simula upload file
        echo "<h3>3. Simulazione Upload</h3>";
        $this->simulateFileUpload();
        
        // Test 4: Verifica controller upload
        echo "<h3>4. Test Controller Upload</h3>";
        $this->testUploadController();
        
        // Test 5: Test AJAX calls
        echo "<h3>5. Test Chiamate AJAX</h3>";
        $this->testAjaxEndpoints();
    }
    
    private function testDirectory($path) 
    {
        if (!is_dir($path)) {
            echo "<div class='error'>❌ Cartella non esistente: $path</div>";
            if (mkdir($path, 0755, true)) {
                echo "<div class='success'>✅ Cartella creata con successo</div>";
                $this->success_fixes[] = "Creata cartella: $path";
            }
        } else {
            echo "<div class='success'>✅ Cartella esistente: $path</div>";
        }
        
        if (is_dir($path)) {
            if (is_writable($path)) {
                echo "<div class='success'>&nbsp;&nbsp;✅ Permessi scrittura: OK</div>";
            } else {
                echo "<div class='error'>&nbsp;&nbsp;❌ Permessi scrittura: FALLITI</div>";
                if (chmod($path, 0755)) {
                    echo "<div class='success'>&nbsp;&nbsp;✅ Permessi corretti automaticamente</div>";
                }
            }
            
            // Test creazione file temporaneo
            $test_file = $path . '/test_' . uniqid() . '.txt';
            if (file_put_contents($test_file, 'test') !== false) {
                echo "<div class='success'>&nbsp;&nbsp;✅ Test scrittura file: OK</div>";
                unlink($test_file);
            } else {
                echo "<div class='error'>&nbsp;&nbsp;❌ Test scrittura file: FALLITO</div>";
            }
        }
    }
    
    private function simulateFileUpload() 
    {
        // Crea un'immagine di test
        $test_image_path = $this->module_path . '/test_image.jpg';
        
        // Crea immagine 100x100 pixel
        $image = imagecreatetruecolor(100, 100);
        $blue = imagecolorallocate($image, 0, 100, 200);
        imagefill($image, 0, 0, $blue);
        
        if (imagejpeg($image, $test_image_path)) {
            echo "<div class='success'>✅ Immagine di test creata</div>";
            
            // Test dimensioni e tipo
            $image_info = getimagesize($test_image_path);
            if ($image_info) {
                echo "<div class='info'>📏 Dimensioni: {$image_info[0]}x{$image_info[1]}</div>";
                echo "<div class='info'>📄 Tipo MIME: {$image_info['mime']}</div>";
            }
            
            // Test spostamento file
            $upload_dir = _PS_IMG_DIR_ . 'art_puzzle/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $new_path = $upload_dir . 'test_upload_' . uniqid() . '.jpg';
            if (copy($test_image_path, $new_path)) {
                echo "<div class='success'>✅ Test spostamento file: OK</div>";
                unlink($new_path);
            } else {
                echo "<div class='error'>❌ Test spostamento file: FALLITO</div>";
            }
            
            unlink($test_image_path);
        } else {
            echo "<div class='error'>❌ Impossibile creare immagine di test</div>";
        }
        
        imagedestroy($image);
    }
    
    private function testUploadController() 
    {
        $controller_path = $this->module_path . '/controllers/front/UploadController.php';
        
        if (!file_exists($controller_path)) {
            echo "<div class='error'>❌ UploadController.php non trovato</div>";
            return;
        }
        
        $content = file_get_contents($controller_path);
        
        // Verifica sintassi PHP
        $old_error_level = error_reporting(0);
        $syntax_check = php_check_syntax($controller_path);
        error_reporting($old_error_level);
        
        if ($syntax_check) {
            echo "<div class='success'>✅ Sintassi PHP: OK</div>";
        } else {
            echo "<div class='error'>❌ Errori sintassi PHP nel controller</div>";
        }
        
        // Verifica metodi essenziali
        $required_methods = [
            'postProcess' => 'Gestione richieste POST',
            'displayAjax' => 'Risposta AJAX', 
            'initContent' => 'Inizializzazione contenuto'
        ];
        
        foreach ($required_methods as $method => $description) {
            if (strpos($content, "function $method") !== false) {
                echo "<div class='success'>✅ Metodo $method presente ($description)</div>";
            } else {
                echo "<div class='error'>❌ Metodo $method mancante ($description)</div>";
                $this->errors[] = "UploadController: metodo $method mancante";
            }
        }
        
        // Verifica gestione $_FILES
        if (strpos($content, '$_FILES') !== false) {
            echo "<div class='success'>✅ Gestione \$_FILES presente</div>";
        } else {
            echo "<div class='error'>❌ Gestione \$_FILES mancante</div>";
        }
        
        // Test URL routing
        $this->testControllerRouting('Upload');
    }
    
    private function testControllerRouting($controller_name) 
    {
        try {
            $link = new Link();
            $module_link = $link->getModuleLink('art_puzzle', $controller_name);
            
            if ($module_link) {
                echo "<div class='success'>✅ URL Controller generato: <code>$module_link</code></div>";
                
                // Test connessione HTTP
                $this->testHttpConnection($module_link);
            } else {
                echo "<div class='error'>❌ Impossibile generare URL controller</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ Errore routing: " . $e->getMessage() . "</div>";
        }
    }
    
    private function testHttpConnection($url) 
    {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                echo "<div class='error'>&nbsp;&nbsp;❌ Errore connessione: $error</div>";
            } elseif ($http_code == 200) {
                echo "<div class='success'>&nbsp;&nbsp;✅ Connessione HTTP: OK (200)</div>";
            } else {
                echo "<div class='warning'>&nbsp;&nbsp;⚠️ HTTP Code: $http_code</div>";
            }
        } else {
            echo "<div class='warning'>&nbsp;&nbsp;⚠️ cURL non disponibile per test connessione</div>";
        }
    }
    
    private function testAjaxEndpoints() 
    {
        $ajax_endpoints = [
            'Upload' => 'Caricamento immagini',
            'Preview' => 'Generazione anteprima',
            'Cart' => 'Aggiunta al carrello'
        ];
        
        foreach ($ajax_endpoints as $endpoint => $description) {
            echo "<div class='info'>🔍 Test endpoint $endpoint ($description)</div>";
            $this->testControllerRouting($endpoint);
        }
    }
    
    private function checkJavaScriptFiles() 
    {
        echo "<h2>📜 Controllo File JavaScript</h2>";
        
        $js_files = [
            'views/js/front.js',
            'views/js/preview-generator.js',
            'views/js/cropper-integration.js'
        ];
        
        foreach ($js_files as $js_file) {
            $js_path = $this->module_path . '/' . $js_file;
            
            if (file_exists($js_path)) {
                $content = file_get_contents($js_path);
                echo "✅ $js_file - PRESENTE<br>";
                
                // Verifica presenza jQuery e AJAX
                if (strpos($content, 'jQuery') !== false || strpos($content, '$') !== false) {
                    echo "&nbsp;&nbsp;✅ jQuery utilizzato<br>";
                } else {
                    $this->warnings[] = "$js_file: jQuery non rilevato";
                }
                
                if (strpos($content, 'ajax') !== false) {
                    echo "&nbsp;&nbsp;✅ Chiamate AJAX presenti<br>";
                } else {
                    $this->warnings[] = "$js_file: chiamate AJAX non rilevate";
                }
                
            } else {
                echo "❌ $js_file - NON TROVATO<br>";
                $this->errors[] = "File JavaScript mancante: $js_file";
            }
        }
    }
    
    private function checkImageUploadPaths() 
    {
        echo "<h2>🖼️ Controllo Percorsi Upload Immagini</h2>";
        
        $upload_dirs = [
            '/img/art_puzzle/',
            '/img/art_puzzle/uploads/',
            '/img/art_puzzle/temp/',
            '/modules/art_puzzle/uploads/',
            '/modules/art_puzzle/img/'
        ];
        
        foreach ($upload_dirs as $dir) {
            $full_path = $this->prestashop_root . $dir;
            
            if (is_dir($full_path)) {
                if (is_writable($full_path)) {
                    echo "✅ $dir - ESISTE E SCRIVIBILE<br>";
                } else {
                    echo "⚠️ $dir - ESISTE MA NON SCRIVIBILE<br>";
                    $this->warnings[] = "Cartella non scrivibile: $dir";
                }
            } else {
                echo "❌ $dir - NON ESISTE<br>";
                $this->fixes[] = "Creare cartella: $dir";
            }
        }
        
        // Controlla dimensioni massime upload
        $max_size = ini_get('upload_max_filesize');
        $post_max = ini_get('post_max_size');
        
        echo "<br>📊 Limiti Upload Server:<br>";
        echo "&nbsp;&nbsp;• upload_max_filesize: $max_size<br>";
        echo "&nbsp;&nbsp;• post_max_size: $post_max<br>";
        
        if ($this->convertToBytes($max_size) < 10485760) { // 10MB
            $this->warnings[] = "upload_max_filesize troppo basso per immagini grandi";
        }
    }
    
    private function checkTemplates() 
    {
        echo "<h2>🎨 Controllo Template</h2>";
        
        $templates = [
            'views/templates/front/customizer.tpl',
            'views/templates/front/format.tpl',
            'views/templates/front/box.tpl',
            'views/templates/hook/displayProductButtons.tpl'
        ];
        
        foreach ($templates as $template) {
            $template_path = $this->module_path . '/' . $template;
            
            if (file_exists($template_path)) {
                $content = file_get_contents($template_path);
                echo "✅ $template - PRESENTE<br>";
                
                // Verifica presenza form upload
                if (strpos($content, 'input') !== false && strpos($content, 'file') !== false) {
                    echo "&nbsp;&nbsp;✅ Form upload rilevato<br>";
                } else if (strpos($template, 'customizer') !== false) {
                    $this->warnings[] = "$template: form upload non rilevato";
                }
                
            } else {
                echo "❌ $template - NON TROVATO<br>";
                $this->errors[] = "Template mancante: $template";
            }
        }
    }
    
    private function checkPermissions() 
    {
        echo "<h2>🔐 Controllo Permessi</h2>";
        
        $check_paths = [
            $this->module_path,
            $this->module_path . '/controllers',
            $this->module_path . '/views',
            $this->module_path . '/classes'
        ];
        
        foreach ($check_paths as $path) {
            if (is_dir($path)) {
                $perms = substr(sprintf('%o', fileperms($path)), -4);
                if (is_writable($path)) {
                    echo "✅ $path - Permessi: $perms (OK)<br>";
                } else {
                    echo "⚠️ $path - Permessi: $perms (Problema scrittura)<br>";
                    $this->warnings[] = "Problema permessi: $path";
                }
            }
        }
    }
    
    private function checkDatabaseTables() 
    {
        echo "<h2>🗄️ Controllo Tabelle Database</h2>";
        
        try {
            // Verifica se il modulo è installato
            $module_check = Db::getInstance()->getValue(
                "SELECT id_module FROM " . _DB_PREFIX_ . "module WHERE name = 'art_puzzle'"
            );
            
            if ($module_check) {
                echo "✅ Modulo registrato nel database (ID: $module_check)<br>";
            } else {
                echo "❌ Modulo non registrato nel database<br>";
                $this->errors[] = "Modulo non installato correttamente nel database";
            }
            
            // Verifica tabelle custom se esistono
            $tables = Db::getInstance()->executeS("SHOW TABLES LIKE '" . _DB_PREFIX_ . "art_puzzle%'");
            if ($tables) {
                foreach ($tables as $table) {
                    $table_name = current($table);
                    echo "✅ Tabella trovata: $table_name<br>";
                }
            } else {
                echo "ℹ️ Nessuna tabella custom trovata<br>";
            }
            
        } catch (Exception $e) {
            $this->errors[] = "Errore controllo database: " . $e->getMessage();
        }
    }
    
    private function displayResults() 
    {
        echo "<hr><h2>📋 Riepilogo Diagnostica</h2>";
        
        if (empty($this->errors) && empty($this->warnings)) {
            echo "<div style='color: green; font-weight: bold;'>🎉 Nessun problema critico rilevato!</div>";
        } else {
            if (!empty($this->errors)) {
                echo "<h3 style='color: red;'>❌ Errori Critici:</h3>";
                foreach ($this->errors as $error) {
                    echo "<div style='color: red;'>• $error</div>";
                }
            }
            
            if (!empty($this->warnings)) {
                echo "<h3 style='color: orange;'>⚠️ Avvisi:</h3>";
                foreach ($this->warnings as $warning) {
                    echo "<div style='color: orange;'>• $warning</div>";
                }
            }
        }
    }
    
    private function autoFixProblems() 
    {
        if (empty($this->fixes) && empty($this->errors)) {
            return;
        }
        
        echo "<hr><h2>🔧 Auto-Fix Problemi</h2>";
        
        // Crea cartelle mancanti
        $this->createMissingDirectories();
        
        // Genera file mancanti
        $this->generateMissingFiles();
        
        // Correggi permessi
        $this->fixPermissions();
        
        echo "<div style='color: green; font-weight: bold; margin-top: 20px;'>✅ Auto-fix completato!</div>";
        echo "<div style='margin-top: 10px;'><strong>Prossimi passi:</strong><br>";
        echo "1. Reinstalla il modulo da BackOffice PrestaShop<br>";
        echo "2. Verifica che tutti gli hook siano attivi<br>";
        echo "3. Testa il caricamento immagini dal frontend<br>";
        echo "4. Controlla i log di errore del server</div>";
    }
    
    private function createMissingDirectories() 
    {
        $dirs_to_create = [
            '/img/art_puzzle/',
            '/img/art_puzzle/uploads/',
            '/img/art_puzzle/temp/',
            '/modules/art_puzzle/uploads/',
            '/modules/art_puzzle/img/'
        ];
        
        foreach ($dirs_to_create as $dir) {
            $full_path = $this->prestashop_root . $dir;
            if (!is_dir($full_path)) {
                if (mkdir($full_path, 0755, true)) {
                    echo "✅ Creata cartella: $dir<br>";
                    // Crea file .htaccess per sicurezza
                    file_put_contents($full_path . '/.htaccess', "Options -Indexes\nOrder deny,allow\nDeny from all");
                } else {
                    echo "❌ Impossibile creare: $dir<br>";
                }
            }
        }
    }
    
    private function generateMissingFiles() 
    {
        // Genera index.php di sicurezza nelle cartelle
        $security_dirs = [
            '/modules/art_puzzle/uploads/',
            '/modules/art_puzzle/img/',
            '/img/art_puzzle/',
            '/img/art_puzzle/uploads/',
            '/img/art_puzzle/temp/'
        ];
        
        $index_content = "<?php\n// Security file - prevents directory listing\nheader('HTTP/1.0 403 Forbidden');\nexit('Access denied');\n?>";
        
        foreach ($security_dirs as $dir) {
            $full_path = $this->prestashop_root . $dir;
            if (is_dir($full_path) && !file_exists($full_path . '/index.php')) {
                file_put_contents($full_path . '/index.php', $index_content);
                echo "✅ Creato index.php di sicurezza in: $dir<br>";
            }
        }
    }
    
    private function fixPermissions() 
    {
        $paths_to_fix = [
            $this->prestashop_root . '/img/art_puzzle/',
            $this->prestashop_root . '/modules/art_puzzle/uploads/',
            $this->module_path . '/views/js/',
            $this->module_path . '/views/css/'
        ];
        
        foreach ($paths_to_fix as $path) {
            if (is_dir($path)) {
                chmod($path, 0755);
                echo "✅ Permessi corretti per: " . basename($path) . "<br>";
            }
        }
    }
    
    private function convertToBytes($val) 
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int) $val;
        
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        
        return $val;
    }
    
    /**
     * Genera report dettagliato per sviluppatore
     */
    public function generateDetailedReport() 
    {
        $report = "\n\n=== ART PUZZLE - DETAILED DEBUG REPORT ===\n";
        $report .= "Data: " . date('Y-m-d H:i:s') . "\n";
        $report .= "PrestaShop Root: " . $this->prestashop_root . "\n";
        $report .= "Module Path: " . $this->module_path . "\n\n";
        
        $report .= "ERRORI CRITICI:\n";
        foreach ($this->errors as $error) {
            $report .= "- $error\n";
        }
        
        $report .= "\nAVVISI:\n";
        foreach ($this->warnings as $warning) {
            $report .= "- $warning\n";
        }
        
        $report .= "\nCORREZIONI APPLICATE:\n";
        foreach ($this->fixes as $fix) {
            $report .= "- $fix\n";
        }
        
        // Salva report
        file_put_contents($this->module_path . '/debug_report.txt', $report);
        echo "<br>📄 Report dettagliato salvato in: debug_report.txt<br>";
    }
}

// Esecuzione dello script
try {
    $debugger = new ArtPuzzleDebugFixer();
    $debugger->runDiagnostic();
    $debugger->generateDetailedReport();
    
    echo "<hr>";
    echo "<h2>🎯 Diagnosi Problemi Caricamento Immagini</h2>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007cba;'>";
    echo "<strong>Possibili cause del problema caricamento immagini:</strong><br><br>";
    echo "1. <strong>Controller UploadController.php</strong> - Metodi mancanti o errati<br>";
    echo "2. <strong>Hook displayHeader</strong> - Non carica correttamente CSS/JS<br>";
    echo "3. <strong>Cartelle upload</strong> - Permessi insufficienti o cartelle mancanti<br>";
    echo "4. <strong>JavaScript</strong> - Errori nelle chiamate AJAX o form non collegati<br>";
    echo "5. <strong>Template customizer.tpl</strong> - Form upload mal configurato<br>";
    echo "6. <strong>Routing PrestaShop</strong> - URL controller non correttamente registrati<br>";
    echo "</div>";
    
    echo "<br><strong>🔍 Per debug avanzato, controlla:</strong><br>";
    echo "• Console browser (F12) per errori JavaScript<br>";
    echo "• Log errori Apache/Nginx<br>";
    echo "• Log errori PrestaShop in /var/logs/<br>";
    echo "• Network tab per chiamate AJAX fallite<br>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-weight: bold;'>❌ Errore durante l'esecuzione: " . $e->getMessage() . "</div>";
}

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #007cba; }
h2 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 5px; }
h3 { color: #555; }
</style>";
?>