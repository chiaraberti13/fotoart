<?php
/**
 * Debug avanzato per Art Puzzle AJAX
 * Sostituisci temporaneamente il file ajax.php con questo per il debug
 */

// Attiva tutti gli errori
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

class ArtPuzzleAjaxModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public $display_header = false;
    public $display_footer = false;
    protected $content_type = 'application/json';
    public $ajax = true;
    
    private $debugLog = [];
    
    private function debug($message) {
        $this->debugLog[] = date('H:i:s') . " - " . $message;
        error_log("ART_PUZZLE_DEBUG: $message");
    }
    
    public function init()
    {
        $this->debug("=== INIT CHIAMATO ===");
        
        try {
            parent::init();
            $this->debug("parent::init() completato");
        } catch (Exception $e) {
            $this->debug("Errore in parent::init(): " . $e->getMessage());
            $this->returnDebugResponse(false, "Errore init: " . $e->getMessage());
        }
        
        // Log parametri ricevuti
        $this->debug("GET params: " . json_encode($_GET));
        $this->debug("POST params: " . json_encode($_POST));
        $this->debug("SERVER['HTTP_X_REQUESTED_WITH']: " . ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'non presente'));
        
        // Verifica AJAX
        $isXhr = $this->isXmlHttpRequest();
        $hasAjaxParam = Tools::getValue('ajax');
        $this->debug("isXmlHttpRequest: " . ($isXhr ? 'SI' : 'NO'));
        $this->debug("ajax parameter: " . ($hasAjaxParam ? 'SI' : 'NO'));
        
        if (!$isXhr && !$hasAjaxParam) {
            $this->debug("BLOCCATO: Non è una richiesta AJAX");
            $this->returnDebugResponse(false, 'Richiesta non valida - Non AJAX');
        }
        
        // Verifica token
        $hasPreviewMode = Tools::getValue('preview_mode');
        $receivedToken = Tools::getValue('token');
        $expectedToken = Tools::getToken(false);
        
        $this->debug("preview_mode: " . ($hasPreviewMode ? 'SI' : 'NO'));
        $this->debug("Token ricevuto: " . ($receivedToken ?? 'NESSUNO'));
        $this->debug("Token atteso: " . $expectedToken);
        $this->debug("Token match: " . ($receivedToken == $expectedToken ? 'SI' : 'NO'));
        
        if (!$hasPreviewMode && (!$receivedToken || $receivedToken != $expectedToken)) {
            $this->debug("BLOCCATO: Token non valido");
            $this->returnDebugResponse(false, "Token di sicurezza non valido. Ricevuto: '$receivedToken', Atteso: '$expectedToken'");
        }
        
        $this->debug("init() completato con successo");
    }
    
    public function postProcess()
    {
        $this->debug("=== POSTPROCESS CHIAMATO ===");
        
        $action = Tools::getValue('action');
        $this->debug("Azione richiesta: " . ($action ?? 'NESSUNA'));
        
        if (!Tools::isSubmit('action')) {
            $this->debug("ERRORE: Nessuna azione specificata");
            $this->returnDebugResponse(false, 'Nessuna azione specificata');
            return;
        }
        
        try {
            $this->debug("Eseguo azione: $action");
            
            switch ($action) {
                case 'getFonts':
                    $this->debug("Chiamata handleGetFonts()");
                    $this->handleGetFonts();
                    break;
                
                case 'getBoxColors':
                    $this->debug("Chiamata handleGetBoxColors()");
                    $this->handleGetBoxColors();
                    break;
                
                case 'checkDirectoryPermissions':
                    $this->debug("Chiamata handleCheckDirectoryPermissions()");
                    $this->handleCheckDirectoryPermissions();
                    break;
                
                case 'generateBoxPreview':
                    $this->debug("Chiamata handleGenerateBoxPreview()");
                    $this->handleGenerateBoxPreview();
                    break;
                    
                case 'uploadImage':
                    $this->debug("Chiamata handleUploadImage()");
                    $this->handleUploadImage();
                    break;
                
                default:
                    $this->debug("ERRORE: Azione non valida");
                    $this->returnDebugResponse(false, 'Azione non valida: ' . $action);
            }
        } catch (Exception $e) {
            $this->debug("ECCEZIONE: " . $e->getMessage());
            $this->debug("Stack trace: " . $e->getTraceAsString());
            $this->returnDebugResponse(false, 'Errore: ' . $e->getMessage());
        }
    }
    
    protected function returnDebugResponse($success, $message, $data = [])
    {
        $this->debug("returnDebugResponse chiamata - Success: " . ($success ? 'SI' : 'NO'));
        
        $response = [
            'success' => $success,
            'message' => $message,
            'debug' => $this->debugLog,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($data)) {
            $response['data'] = $data;
        }
        
        // Header
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        
        if (ob_get_length()) {
            ob_clean();
        }
        
        $json = json_encode($response, JSON_PRETTY_PRINT);
        $this->debug("JSON generato: " . strlen($json) . " bytes");
        
        die($json);
    }
    
    protected function isXmlHttpRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
    
    // Funzioni handle semplificate per il debug
    protected function handleGetFonts()
    {
        $this->debug("handleGetFonts() eseguita");
        $fonts = Configuration::get('ART_PUZZLE_FONTS');
        $this->debug("Fonts dalla config: " . ($fonts ?? 'NULL'));
        $fonts_array = $fonts ? explode(',', $fonts) : [];
        $this->debug("Fonts array: " . json_encode($fonts_array));
        $this->returnDebugResponse(true, 'Font caricati con successo', $fonts_array);
    }
    
    protected function handleGetBoxColors()
    {
        $this->debug("handleGetBoxColors() eseguita");
        $box_colors = Configuration::get('ART_PUZZLE_BOX_COLORS');
        $this->debug("Box colors dalla config: " . ($box_colors ?? 'NULL'));
        $colors_array = json_decode($box_colors, true) ?: [];
        $this->debug("Colors array: " . json_encode($colors_array));
        $this->returnDebugResponse(true, 'Colori caricati con successo', $colors_array);
    }
    
    protected function handleCheckDirectoryPermissions()
    {
        $this->debug("handleCheckDirectoryPermissions() eseguita");
        $directories = [
            'upload' => _PS_MODULE_DIR_.'art_puzzle/upload/',
            'logs' => _PS_MODULE_DIR_.'art_puzzle/logs/',
            'fonts' => _PS_MODULE_DIR_.'art_puzzle/views/fonts/'
        ];
        
        $status = [];
        foreach ($directories as $name => $path) {
            $exists = file_exists($path);
            $writable = $exists && is_writable($path);
            $status[$name] = [
                'path' => $path,
                'exists' => $exists,
                'writable' => $writable
            ];
            $this->debug("$name: exists=" . ($exists?'SI':'NO') . ", writable=" . ($writable?'SI':'NO'));
        }
        
        $this->returnDebugResponse(true, 'Verifica permessi completata', $status);
    }
    
    protected function handleGenerateBoxPreview()
    {
        $this->debug("handleGenerateBoxPreview() eseguita");
        $text = Tools::getValue('text', 'Il mio puzzle');
        $color = Tools::getValue('color', '#ffffff');
        $font = Tools::getValue('font', '');
        $template = Tools::getValue('template', 'classic');
        
        $this->debug("Parametri: text=$text, color=$color, font=$font, template=$template");
        
        $this->returnDebugResponse(true, 'Preview generata (simulata)', [
            'text' => $text,
            'color' => $color,
            'font' => $font,
            'template' => $template
        ]);
    }
    
    protected function handleUploadImage()
    {
        $this->debug("handleUploadImage() eseguita");
        $this->debug("FILES: " . json_encode(array_keys($_FILES)));
        $this->returnDebugResponse(false, 'Upload non implementato in modalità debug');
    }
}
