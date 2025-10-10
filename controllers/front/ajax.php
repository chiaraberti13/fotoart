<?php

require_once _PS_MODULE_DIR_ . 'art_puzzle/autoload.php';

use ArtPuzzle\ArtPuzzleAjaxErrorHandler;
use ArtPuzzle\ArtPuzzleLogger;
use ArtPuzzle\PDFGeneratorPuzzle;
use ArtPuzzle\PuzzleBoxManager;
use ArtPuzzle\PuzzleFormatManager;
use ArtPuzzle\PuzzleImageProcessor;

/**
 * Art Puzzle - AJAX Controller
 */

class ArtPuzzleAjaxModuleFrontController extends ModuleFrontController
{
    /** @var bool Disattiva il rendering della colonna sinistra */
    public $display_column_left = false;
    
    /** @var bool Disattiva il rendering della colonna destra */
    public $display_column_right = false;
    
    /** @var bool Disattiva il rendering dell'header */
    public $display_header = false;
    
    /** @var bool Disattiva il rendering del footer */
    public $display_footer = false;
    
    /** @var string Imposta il content-type come JSON */
    protected $content_type = 'application/json';
    
    /** @var bool Imposta la richiesta come AJAX */
    public $ajax = true;
    
    /**
     * Inizializzazione del controller
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        ArtPuzzleAjaxErrorHandler::register(function (\Throwable $throwable) {
            $this->handleAjaxThrowable($throwable);
        });

        // Verifica se è una richiesta AJAX
        if (!$this->isXmlHttpRequest() && !Tools::getValue('ajax')) {
    $this->returnResponse(false, 'Richiesta non valida');
    // exit; <- rimosso, returnResponse fa già die()
}
        
        // Verifica token CSRF eccetto per le richieste di visualizzazione in anteprima
        if (!Tools::getValue('preview_mode') && 
    (!Tools::getValue('token') || Tools::getValue('token') != Tools::getToken(false))) {
    $this->returnResponse(false, 'Token di sicurezza non valido');
    // exit; <- rimosso
}
    }
    
    /**
     * Gestisce le richieste POST
     */
    public function postProcess()
    {
        // Verifica che ci sia un'azione specificata
        if (!Tools::isSubmit('action')) {
            $this->returnResponse(false, 'Nessuna azione specificata');
            return;
        }
        
        $action = Tools::getValue('action');

        $this->executeAjax(function () use ($action) {
            switch ($action) {
                case 'savePuzzleCustomization':
                    $this->handleSavePuzzleCustomization();
                    break;

                case 'checkImageQuality':
                    $this->handleCheckImageQuality();
                    break;

                case 'getBoxColors':
                    $this->handleGetBoxColors();
                    break;

                case 'getFonts':
                    $this->handleGetFonts();
                    break;

                case 'checkDirectoryPermissions':
                    $this->handleCheckDirectoryPermissions();
                    break;

                // Nuove azioni per il puzzle personalizzato
                case 'uploadImage':
                    $this->handleUploadImage();
                    break;

                case 'getPuzzleFormats':
                    $this->handleGetPuzzleFormats();
                    break;

                case 'getBoxTemplates':
                    $this->handleGetBoxTemplates();
                    break;

                case 'generateBoxPreview':
                    $this->handleGenerateBoxPreview();
                    break;

                case 'generatePuzzlePreview':
                    $this->handleGeneratePuzzlePreview();
                    break;

                case 'generateSummaryPreview':
                    $this->handleGenerateSummaryPreview();
                    break;

                case 'add_to_cart':
                    $this->handleAddToCart();
                    break;

                default:
                    $this->returnResponse(false, 'Azione non valida: ' . $action);
            }
        });
    }
    
    /**
     * Restituisce una risposta JSON
     */
    protected function returnResponse($success, $message, $data = [])
{
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if (!empty($data)) {
        $response['data'] = $data;
    }
    
    // Imposta gli header corretti per JSON
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    
    // Pulisce eventuali output buffer precedenti
    if (ob_get_length()) {
        ob_clean();
    }
    
    die(json_encode($response));
}

    /**
     * Esegue una callback incapsulando la gestione delle eccezioni.
     */
    protected function executeAjax(callable $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $throwable) {
            $this->handleAjaxThrowable($throwable);
        }

        return null;
    }

    protected function handleAjaxThrowable(\Throwable $throwable)
    {
        $this->returnResponse(false, $this->formatAjaxErrorMessage($throwable));
    }

    protected function formatAjaxErrorMessage(\Throwable $throwable)
    {
        return sprintf('Errore: %s', Tools::safeOutput($throwable->getMessage()));
    }
    
    /**
     * Verifica se è una richiesta AJAX
     */
    protected function isXmlHttpRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
    
    /**
     * Gestisce il caricamento dell'immagine
     */
    protected function handleUploadImage()
    {
        $this->executeAjax(function () {
                // Debug iniziale
                ArtPuzzleLogger::log('=== INIZIO UPLOAD IMMAGINE ===', 'INFO');
                ArtPuzzleLogger::log('FILES ricevuti: ' . print_r($_FILES, true), 'DEBUG');
                ArtPuzzleLogger::log('POST ricevuti: ' . print_r($_POST, true), 'DEBUG');

                // Verifica presenza file con nomi multipli possibili
                $file = null;
                $fileKeys = ['image', 'puzzle_image', 'file', 'upload'];

                foreach ($fileKeys as $key) {
                    if (isset($_FILES[$key]) && !empty($_FILES[$key]['name'])) {
                        $file = $_FILES[$key];
                        ArtPuzzleLogger::log("File trovato con chiave: $key", 'INFO');
                        break;
                    }
                }

                if (!$file) {
                    ArtPuzzleLogger::log('ERRORE: Nessun file trovato in $_FILES', 'ERROR');
                    $this->returnResponse(false, 'Nessuna immagine caricata. Verifica il form di upload.');
                    return;
                }

                // Verifica errori di upload
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'File troppo grande (limite PHP)',
                        UPLOAD_ERR_FORM_SIZE => 'File troppo grande (limite form)',
                        UPLOAD_ERR_PARTIAL => 'Upload parziale',
                        UPLOAD_ERR_NO_FILE => 'Nessun file',
                        UPLOAD_ERR_NO_TMP_DIR => 'Directory temporanea mancante',
                        UPLOAD_ERR_CANT_WRITE => 'Impossibile scrivere su disco',
                        UPLOAD_ERR_EXTENSION => 'Upload bloccato da estensione PHP'
                    ];

                    $message = $errorMessages[$file['error']] ?? 'Errore sconosciuto';
                    ArtPuzzleLogger::log("Errore upload: {$file['error']} - $message", 'ERROR');
                    $this->returnResponse(false, "Errore upload: $message");
                    return;
                }

                // Validazione dimensione
                $maxSize = (int)Configuration::get('ART_PUZZLE_MAX_UPLOAD_SIZE', 20) * 1024 * 1024;
                if ($file['size'] > $maxSize) {
                    $this->returnResponse(false, 'File troppo grande. Massimo: ' . round($maxSize/1024/1024, 1) . 'MB');
                    return;
                }

                // Validazione tipo file con controllo MIME più robusto
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowedExtensions)) {
                    $this->returnResponse(false, 'Formato non supportato. Usa: ' . implode(', ', $allowedExtensions));
                    return;
                }

                $allowedImageTypes = [
                    IMAGETYPE_JPEG => 'jpg',
                    IMAGETYPE_PNG => 'png',
                    IMAGETYPE_GIF => 'gif',
                ];

                $detectedType = @exif_imagetype($file['tmp_name']);
                if ($detectedType === false && function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    if ($finfo) {
                        $mime = finfo_file($finfo, $file['tmp_name']);
                        finfo_close($finfo);
                        $detectedType = array_search($mime, [
                            IMAGETYPE_JPEG => 'image/jpeg',
                            IMAGETYPE_PNG => 'image/png',
                            IMAGETYPE_GIF => 'image/gif',
                        ], true);
                    }
                }

                if ($detectedType === false || !isset($allowedImageTypes[$detectedType])) {
                    ArtPuzzleLogger::log('ERRORE: Tipo immagine non valido o non consentito per ' . $file['tmp_name'], 'ERROR');
                    $this->returnResponse(false, 'Il file caricato non è un\'immagine supportata');
                    return;
                }

                $extension = $allowedImageTypes[$detectedType];

                // Verifica che sia realmente un'immagine usando getimagesize
                $imageInfo = @getimagesize($file['tmp_name']);
                if ($imageInfo === false) {
                    ArtPuzzleLogger::log('ERRORE: getimagesize fallita per ' . $file['tmp_name'], 'ERROR');
                    $this->returnResponse(false, 'Il file non è un\'immagine valida');
                    return;
                }

                // Verifica dimensioni immagine
                $width = (int) $imageInfo[0];
                $height = (int) $imageInfo[1];
                $minWidth = (int) Configuration::get('ART_PUZZLE_MIN_UPLOAD_WIDTH', null, null, null, 200);
                $minHeight = (int) Configuration::get('ART_PUZZLE_MIN_UPLOAD_HEIGHT', null, null, null, 200);
                $maxWidth = (int) Configuration::get('ART_PUZZLE_MAX_UPLOAD_WIDTH', null, null, null, 8000);
                $maxHeight = (int) Configuration::get('ART_PUZZLE_MAX_UPLOAD_HEIGHT', null, null, null, 8000);

                if ($width < $minWidth || $height < $minHeight) {
                    $this->returnResponse(false, sprintf('L\'immagine deve essere almeno %dx%d pixel', $minWidth, $minHeight));
                    return;
                }

                if ($width > $maxWidth || $height > $maxHeight) {
                    $this->returnResponse(false, sprintf('L\'immagine non può superare %dx%d pixel', $maxWidth, $maxHeight));
                    return;
                }

                // Preparazione directory upload
                $uploadDir = _PS_MODULE_DIR_ . 'art_puzzle/upload/';
                if (!file_exists($uploadDir)) {
                    if (!mkdir($uploadDir, 0755, true)) {
                        ArtPuzzleLogger::log('ERRORE: Impossibile creare directory ' . $uploadDir, 'ERROR');
                        $this->returnResponse(false, 'Errore nella creazione directory upload');
                        return;
                    }
                }

                if (!is_writable($uploadDir)) {
                    ArtPuzzleLogger::log('ERRORE: Directory non scrivibile ' . $uploadDir, 'ERROR');
                    $this->returnResponse(false, 'Directory upload non scrivibile');
                    return;
                }

                // Genera nome file unico e sanificato
                $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
                $sanitizedBaseName = Tools::link_rewrite($originalName);
                if (empty($sanitizedBaseName)) {
                    $sanitizedBaseName = 'immagine';
                }
                $sanitizedBaseName = Tools::substr($sanitizedBaseName, 0, 60);
                $filename = sprintf(
                    'puzzle_%s_%s_%s.%s',
                    date('YmdHis'),
                    $sanitizedBaseName,
                    uniqid(),
                    $extension
                );
                $destination = $uploadDir . $filename;

                // Sposta il file
                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    ArtPuzzleLogger::log('ERRORE: move_uploaded_file fallita da ' . $file['tmp_name'] . ' a ' . $destination, 'ERROR');
                    $this->returnResponse(false, 'Errore nel salvataggio file');
                    return;
                }

                // Salva in sessione
                $this->context->cookie->__set('art_puzzle_uploaded_image', $filename);
                $this->context->cookie->write();

                // Genera URL per anteprima
                $imageUrl = $this->context->shop->getBaseURL(true) . 'modules/art_puzzle/upload/' . $filename;

                ArtPuzzleLogger::log('SUCCESS: Immagine salvata - ' . $filename, 'INFO');

                // Risposta successo
                $this->returnResponse(true, 'Immagine caricata con successo', [
                    'filename' => $filename,
                    'url' => $imageUrl,
                    'width' => $width,
                    'height' => $height,
                    'size' => $file['size']
                ]);
        });
    }
    
    /**
     * Gestisce la richiesta dei formati puzzle disponibili
     */
    protected function handleGetPuzzleFormats()
    {
        $this->executeAjax(function () {

                // Controlla se è richiesto un filtro per orientamento
                $orientation = Tools::getValue('orientation', '');

                if (!empty($orientation)) {
                    $formats = PuzzleFormatManager::getFormatsByOrientation($orientation);
                } else {
                    $formats = PuzzleFormatManager::getAllFormats();
                }

                // Aggiungi prezzi e informazioni aggiuntive ai formati
                foreach ($formats as $id => &$format) {
                    // In una versione completa, qui andrebbero recuperati i prezzi dal database
                    // o dalle combinazioni di prodotti. Per ora utilizziamo valori di esempio.
                    $format['price'] = [
                        'value' => 19.99 + (count(explode('_', $id)) * 5),
                        'formatted' => number_format(19.99 + (count(explode('_', $id)) * 5), 2) . ' €'
                    ];
                }

                $this->returnResponse(true, 'Formati puzzle caricati con successo', $formats);
        });
    }
    
    /**
     * Gestisce la richiesta dei template di scatole disponibili
     */
    protected function handleGetBoxTemplates()
    {
        $this->executeAjax(function () {

                $templates = PuzzleBoxManager::getAllBoxTemplates();
                $fonts = PuzzleBoxManager::getAllFonts();

                $this->returnResponse(true, 'Template scatole caricati con successo', [
                    'templates' => $templates,
                    'fonts' => $fonts
                ]);
        });
    }
    
    /**
     * Gestisce la generazione dell'anteprima della scatola
     */
    protected function handleGenerateBoxPreview()
    {
        $this->executeAjax(function () {

                // Recupera i parametri
                $boxData = [
                    'template' => Tools::getValue('template', 'classic'),
                    'color' => Tools::getValue('color', 'white'),
                    'text' => Tools::getValue('text', 'Il mio puzzle'),
                    'font' => Tools::getValue('font', 'default')
                ];

                // Recupera il percorso dell'immagine se necessario
                $imagePath = null;

                if ($boxData['template'] == 'photobox') {
                    // Se è una scatola con foto, recupera l'immagine dalla sessione
                    $imageSessionKey = 'art_puzzle_image';

                    if (isset($this->context->cookie->{$imageSessionKey})) {
                        $imagePath = $this->context->cookie->{$imageSessionKey};

                        // Verifica che il file esista
                        if (!file_exists($imagePath)) {
                            $imagePath = null;
                        }
                    }
                }

                // Genera l'anteprima
                $previewData = PuzzleBoxManager::generateBoxPreview($boxData, $imagePath, true);

                if ($previewData) {
                    // Salva la configurazione della scatola nella sessione
                    $sessionKey = 'art_puzzle_box_data';
                    $this->context->cookie->{$sessionKey} = json_encode($boxData);
                    $this->context->cookie->write();

                    $this->returnResponse(true, 'Anteprima generata con successo', [
                        'preview' => $previewData
                    ]);
                } else {
                    $this->returnResponse(false, 'Impossibile generare l\'anteprima della scatola');
                }
        });
    }
    
    /**
     * Gestisce la generazione dell'anteprima del puzzle
     */

    protected function handleGeneratePuzzlePreview()
    {
        $this->executeAjax(function () {

            // Recupera i parametri
            $formatId = Tools::getValue('format', '');
            $imageBase64 = Tools::getValue('image', '');
            $rotate = (int)Tools::getValue('rotate', 0);
            $flip = Tools::getValue('flip', 'none');
            $cropData = Tools::getValue('cropData', '');
            $boxData = Tools::getValue('boxData', '');
            $brightness = (int)Tools::getValue('brightness', 0);
            $contrast = (int)Tools::getValue('contrast', 0);
            $saturation = (int)Tools::getValue('saturation', 0);

            ArtPuzzleLogger::log('--- Generazione puzzle preview ---', 'INFO');
            ArtPuzzleLogger::log('Format ID: ' . $formatId, 'INFO');
            ArtPuzzleLogger::log('Rotate: ' . $rotate, 'INFO');
            ArtPuzzleLogger::log('Flip: ' . $flip, 'INFO');
            ArtPuzzleLogger::log('Brightness: ' . $brightness, 'INFO');
            ArtPuzzleLogger::log('Contrast: ' . $contrast, 'INFO');
            ArtPuzzleLogger::log('Saturation: ' . $saturation, 'INFO');

            if (empty($formatId)) {
                $this->returnResponse(false, 'Formato puzzle non specificato');
                return;
            }

            // Recupera i dettagli del formato
            $format = PuzzleFormatManager::getFormat($formatId);
            if (!$format) {
                $this->returnResponse(false, 'Formato puzzle non valido');
                return;
            }

            // Gestisce l'immagine: può arrivare base64 o essere già stata caricata
            $imagePath = null;

            if (!empty($imageBase64)) {
                $imagePath = $this->saveBase64Image($imageBase64);
                if (!$imagePath) {
                    $this->returnResponse(false, 'Impossibile salvare l\'immagine fornita');
                    return;
                }
            } else {
                // Se non arriva l'immagine, tenta di recuperarla dalla sessione
                $sessionImageKey = 'art_puzzle_uploaded_image';
                if (isset($this->context->cookie->{$sessionImageKey})) {
                    $imageFilename = $this->context->cookie->{$sessionImageKey};
                    $imagePath = _PS_MODULE_DIR_ . 'art_puzzle/upload/' . $imageFilename;

                    if (!file_exists($imagePath)) {
                        $this->returnResponse(false, 'L\'immagine caricata non è più disponibile. Ricarica il file.');
                        return;
                    }
                }
            }

            if (!$imagePath || !file_exists($imagePath)) {
                $this->returnResponse(false, 'Nessuna immagine disponibile per generare l\'anteprima');
                return;
            }

            ArtPuzzleLogger::log('Immagine utilizzata: ' . $imagePath, 'INFO');

            // Applica il crop se fornito
            $cropOptions = !empty($cropData) ? json_decode($cropData, true) : [];

            if (!empty($cropOptions) && isset($cropOptions['width'], $cropOptions['height'])) {
                $imagePath = $this->applyImageCrop($imagePath, $cropOptions);
                ArtPuzzleLogger::log('Crop applicato: ' . print_r($cropOptions, true), 'INFO');
            }

            // Applica i filtri di luminosità/contrasto/saturazione se richiesti
            if ($brightness !== 0 || $contrast !== 0 || $saturation !== 0) {
                $imagePath = $this->applyImageFilters($imagePath, [
                    'brightness' => $brightness,
                    'contrast' => $contrast,
                    'saturation' => $saturation
                ]);
            }

            // Applica rotazione e flip se richiesti
            if ($rotate !== 0 || $flip !== 'none') {
                $imagePath = $this->applyImageTransformations($imagePath, $rotate, $flip);
            }

            // Genera l'anteprima del puzzle
            $previewData = PuzzleImageProcessor::generatePuzzlePreview([
                'format' => $format,
                'image_path' => $imagePath,
                'rotate' => $rotate,
                'flip' => $flip,
                'brightness' => $brightness,
                'contrast' => $contrast,
                'saturation' => $saturation
            ]);

            if (!$previewData) {
                $this->returnResponse(false, 'Impossibile generare l\'anteprima del puzzle. Riprova.');
                return;
            }

            // Salva i dati nel cookie per utilizzi successivi
            $sessionKey = 'art_puzzle_preview_data';
            $boxSessionKey = 'art_puzzle_box_data';
            $cropSessionKey = 'art_puzzle_crop_data';

            $this->context->cookie->{$sessionKey} = json_encode([
                'format' => $format,
                'preview' => $previewData,
                'image_path' => $imagePath,
                'filters' => [
                    'brightness' => $brightness,
                    'contrast' => $contrast,
                    'saturation' => $saturation
                ]
            ]);
            $this->context->cookie->write();

            if (!empty($boxData)) {
                $boxDataDecoded = json_decode($boxData, true);
                if (!empty($boxDataDecoded)) {
                    $this->context->cookie->{$boxSessionKey} = json_encode($boxDataDecoded);
                }
            }

            if (!empty($cropOptions)) {
                $this->context->cookie->{$cropSessionKey} = json_encode($cropOptions);
            }

            $this->context->cookie->write();

            $this->returnResponse(true, 'Anteprima puzzle generata con successo', [
                'preview' => $previewData,
                'format' => $format,
                'image_path' => $imagePath,
                'filters' => [
                    'brightness' => $brightness,
                    'contrast' => $contrast,
                    'saturation' => $saturation
                ]
            ]);
        });
    }

    /**
     * Gestisce la generazione dell'anteprima riepilogativa
     */

    protected function handleGenerateSummaryPreview()
    {
        $this->executeAjax(function () {

            // Recupera dati dalla sessione
            $imageSessionKey = 'art_puzzle_image';
            $formatSessionKey = 'art_puzzle_format';
            $boxSessionKey = 'art_puzzle_box_data';
            $cropSessionKey = 'art_puzzle_crop';

            $imagePath = isset($this->context->cookie->{$imageSessionKey}) ? $this->context->cookie->{$imageSessionKey} : null;
            $formatId = isset($this->context->cookie->{$formatSessionKey}) ? $this->context->cookie->{$formatSessionKey} : null;
            $boxDataJson = isset($this->context->cookie->{$boxSessionKey}) ? $this->context->cookie->{$boxSessionKey} : null;
            $cropDataJson = isset($this->context->cookie->{$cropSessionKey}) ? $this->context->cookie->{$cropSessionKey} : null;

            if (!$imagePath || !file_exists($imagePath) || !$formatId || !$boxDataJson) {
                $this->returnResponse(false, 'Dati di personalizzazione mancanti');
                return;
            }

            // Decodifica i dati
            $boxData = json_decode($boxDataJson, true);
            $cropData = $cropDataJson ? json_decode($cropDataJson, true) : null;

            if (!$boxData) {
                $this->returnResponse(false, 'Dati della scatola non validi');
                return;
            }

            // Recupera il formato
            $format = PuzzleFormatManager::getFormat($formatId);
            if (!$format) {
                $this->returnResponse(false, 'Formato puzzle non valido');
                return;
            }

            // Genera anteprima puzzle
            $puzzleOptions = [
                'format_id' => $formatId,
                'return_base64' => true
            ];

            // Aggiungi dati di ritaglio se presenti
            if ($cropData) {
                $puzzleOptions['crop'] = true;
                $puzzleOptions['crop_data'] = $cropData;
            }

            $puzzlePreview = PuzzleImageProcessor::processImage($imagePath, null, $puzzleOptions);

            // Genera anteprima scatola
            $boxPreview = PuzzleBoxManager::generateBoxPreview($boxData, $imagePath, true);

                if ($puzzlePreview && $boxPreview) {
                    $this->returnResponse(true, 'Anteprime generate con successo', [
                        'puzzlePreview' => $puzzlePreview,
                        'boxPreview' => $boxPreview,
                        'format' => $format,
                        'boxData' => $boxData
                    ]);
                } else {
                    $this->returnResponse(false, 'Impossibile generare le anteprime');
                }
        });
    }

    /**
     * Gestisce l'aggiunta al carrello
     */
    protected function handleAddToCart()
    {
        $this->executeAjax(function () {
                // Verifica che l'utente abbia confermato la personalizzazione
                if (!Tools::getValue('confirm-customization')) {
                    $this->returnResponse(false, 'È necessario confermare la personalizzazione');
                    return;
                }

                // Recupera l'ID prodotto
                $id_product = (int)Tools::getValue('id_product');
                if (!$id_product) {
                    $this->returnResponse(false, 'ID prodotto non valido');
                    return;
                }

                // Verifica che sia un puzzle personalizzabile
                $module = Module::getInstanceByName('art_puzzle');
                if (!$module->isPuzzleProduct($id_product)) {
                    $this->returnResponse(false, 'Questo prodotto non è personalizzabile come puzzle');
                    return;
                }

                // Recupera dati dalla sessione
                $imageSessionKey = 'art_puzzle_image';
                $formatSessionKey = 'art_puzzle_format';
                $boxSessionKey = 'art_puzzle_box_data';
                $cropSessionKey = 'art_puzzle_crop';

                $imagePath = isset($this->context->cookie->{$imageSessionKey}) ? $this->context->cookie->{$imageSessionKey} : null;
                $formatId = isset($this->context->cookie->{$formatSessionKey}) ? $this->context->cookie->{$formatSessionKey} : null;
                $boxDataJson = isset($this->context->cookie->{$boxSessionKey}) ? $this->context->cookie->{$boxSessionKey} : null;
                $cropDataJson = isset($this->context->cookie->{$cropSessionKey}) ? $this->context->cookie->{$cropSessionKey} : null;

                if (!$imagePath || !file_exists($imagePath) || !$formatId || !$boxDataJson) {
                    $this->returnResponse(false, 'Dati di personalizzazione mancanti');
                    return;
                }

                // Carica le classi necessarie

                // Decodifica i dati
                $boxData = json_decode($boxDataJson, true);
                $cropData = $cropDataJson ? json_decode($cropDataJson, true) : null;

                // Prepara i dati per il salvataggio della personalizzazione
                $format = PuzzleFormatManager::getFormat($formatId);

                if (!$format) {
                    $this->returnResponse(false, 'Formato puzzle non valido');
                    return;
                }

                // Genera un nome file definitivo e copia l'immagine
                $uploadDir = _PS_MODULE_DIR_ . 'art_puzzle/upload/';
                $finalFilename = 'puzzle_' . time() . '_' . Tools::passwdGen(8) . '.png';
                $finalPath = $uploadDir . $finalFilename;

                // Elabora l'immagine con le impostazioni di formato e ritaglio finali
                $processOptions = [
                    'format_id' => $formatId,
                    'quality' => 100
                ];

                if ($cropData) {
                    $processOptions['crop'] = true;
                    $processOptions['crop_data'] = $cropData;
                }

                $processedImage = PuzzleImageProcessor::processImage($imagePath, $finalPath, $processOptions);

                if (!$processedImage) {
                    $this->returnResponse(false, 'Errore durante l\'elaborazione dell\'immagine finale');
                    return;
                }

                // Verifica e crea campi di personalizzazione
                $customization_fields = Db::getInstance()->executeS('
                    SELECT cf.`id_customization_field`, cf.`type`
                    FROM `'._DB_PREFIX_.'customization_field` cf
                    WHERE cf.`id_product` = '.(int)$id_product
                );

                if (!$customization_fields) {
                    // Se non ci sono campi di personalizzazione, creali
                    $this->createCustomizationFields($id_product);

                    // Ricarica i campi
                    $customization_fields = Db::getInstance()->executeS('
                        SELECT cf.`id_customization_field`, cf.`type`
                        FROM `'._DB_PREFIX_.'customization_field` cf
                        WHERE cf.`id_product` = '.(int)$id_product
                    );
                }

                // Assicurati che ci sia un carrello valido
                if (!$this->context->cart->id) {
                    $this->context->cart->add();
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }

                // Registra la personalizzazione
                $customization_id = $this->getOrCreateCustomization($this->context->cart->id, $id_product);

                // Dati completi di personalizzazione
                $customization_data = [
                    'format_id' => $formatId,
                    'format_name' => $format['name'],
                    'dimensions' => $format['dimensions'],
                    'pieces' => $format['pieces'],
                    'orientation' => $format['orientation'],
                    'box_template' => $boxData['template'],
                    'box_color' => $boxData['color'],
                    'box_text' => $boxData['text'],
                    'box_font' => $boxData['font'],
                    'image_path' => $finalPath
                ];

                // Mappa i campi di personalizzazione
                foreach ($customization_fields as $field) {
                    if ($field['type'] == 0) { // Campo File (immagine)
                        $this->saveFileCustomization(
                            $customization_id, 
                            $field['id_customization_field'], 
                            $finalPath, 
                            $finalFilename
                        );
                    } elseif ($field['type'] == 1) { // Campo Testo (dati personalizzazione)
                        $this->saveTextCustomization(
                            $customization_id, 
                            $field['id_customization_field'], 
                            json_encode($customization_data)
                        );
                    }
                }

                // Aggiungi al carrello
                $this->context->cart->updateQty(1, $id_product, 0, $customization_id);

                // Aggiorna il carrello se è già stato creato
                if (isset($this->context->cookie->id_cart)) {
                    $this->context->cart = new Cart($this->context->cookie->id_cart);
                }

                // Registra il successo nel log
                ArtPuzzleLogger::log('Puzzle personalizzato aggiunto al carrello. ID Customization: ' . $customization_id);

                // Invia email di notifica se richiesto
                if (Configuration::get('ART_PUZZLE_SEND_PREVIEW_USER_EMAIL') || 
                    Configuration::get('ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL')) {
                    $this->sendNotifications($customization_data, $finalPath);
                }

                // Pulisci vecchi file temporanei (file più vecchi di 24 ore)
                PuzzleImageProcessor::cleanupTempFiles($uploadDir, 86400); // 86400 secondi = 24 ore

                // Restituisci successo
                $this->returnResponse(true, 'Puzzle personalizzato aggiunto al carrello', [
                    'idCustomization' => $customization_id,
                    'idProduct' => $id_product,
                    'idProductAttribute' => 0, // 0 per i prodotti senza attributi
                    'cartUrl' => $this->context->link->getPageLink('cart', null, null, ['action' => 'show'])
                ]);
        });
    }
    
    /**
     * Gestisce il salvataggio della personalizzazione del puzzle
     */
    protected function handleSavePuzzleCustomization()
    {
        $this->executeAjax(function () {
                // Verifica che l'utente sia loggato
                if (!$this->context->customer->isLogged()) {
                    $this->returnResponse(false, 'Utente non autenticato');
                    return;
                }

                $data = json_decode(Tools::getValue('data'), true);

                if (!$data) {
                    $this->returnResponse(false, 'Dati non validi');
                    return;
                }

                // Verifica ID prodotto
                $product_id = (int)$data['product_id'];
                if (!$product_id) {
                    $this->returnResponse(false, 'ID prodotto non valido');
                    return;
                }

                // Verifica che il prodotto sia un puzzle personalizzabile
                $product_ids = explode(',', Configuration::get('ART_PUZZLE_PRODUCT_IDS'));
                if (!in_array((string)$product_id, $product_ids)) {
                    $this->returnResponse(false, 'Questo prodotto non è personalizzabile');
                    return;
                }

                // Salva l'immagine caricata
                $upload_dir = _PS_MODULE_DIR_.'art_puzzle/upload/';

                // Crea la directory se non esiste
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Genera un nome file unico
                $filename = 'puzzle_'.time().'_'.Tools::passwdGen(8).'.png';
                $filepath = $upload_dir.$filename;

                // Salva l'immagine dal base64
                $image_data = $data['customization']['image'];
                $image_parts = explode(";base64,", $image_data);
                $image_base64 = isset($image_parts[1]) ? $image_parts[1] : null;

                if (!$image_base64) {
                    $this->returnResponse(false, 'Formato immagine non valido');
                    return;
                }

                // Verifica validità immagine
                $image_decoded = base64_decode($image_base64);
                if (!$image_decoded) {
                    $this->returnResponse(false, 'Immagine non valida');
                    return;
                }

                // Verifica che sia un'immagine reale
                $img = @imagecreatefromstring($image_decoded);
                if (!$img) {
                    $this->returnResponse(false, 'Il file caricato non è un\'immagine valida');
                    return;
                }
                imagedestroy($img);

                // Salva l'immagine
                file_put_contents($filepath, $image_decoded);

                // Verifica e crea campi di personalizzazione
                $customization_fields = Db::getInstance()->executeS('
                    SELECT cf.`id_customization_field`, cf.`type`
                    FROM `'._DB_PREFIX_.'customization_field` cf
                    WHERE cf.`id_product` = '.(int)$product_id
                );

                if (!$customization_fields) {
                    // Se non ci sono campi di personalizzazione, creali
                    $this->createCustomizationFields($product_id);

                    // Ricarica i campi
                    $customization_fields = Db::getInstance()->executeS('
                        SELECT cf.`id_customization_field`, cf.`type`
                        FROM `'._DB_PREFIX_.'customization_field` cf
                        WHERE cf.`id_product` = '.(int)$product_id
                    );
                }

                // Assicurati che ci sia un carrello valido
                if (!$this->context->cart->id) {
                    $this->context->cart->add();
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }

                // Registra la personalizzazione
                $customization_id = $this->getOrCreateCustomization($this->context->cart->id, $product_id);

                // Mappa i campi di personalizzazione
                foreach ($customization_fields as $field) {
                    if ($field['type'] == 0) { // Campo File
                        $this->saveFileCustomization(
                            $customization_id, 
                            $field['id_customization_field'], 
                            $filepath, 
                            $filename
                        );
                    } elseif ($field['type'] == 1) { // Campo Testo
                        $boxDetails = json_encode([
                            'text' => $data['customization']['boxText'],
                            'boxColor' => $data['customization']['boxColor'],
                            'textColor' => $data['customization']['textColor'],
                            'font' => $data['customization']['font']
                        ]);

                        $this->saveTextCustomization(
                            $customization_id, 
                            $field['id_customization_field'], 
                            $boxDetails
                        );
                    }
                }

                // Invia email di notifica se richiesto
                if (Configuration::get('ART_PUZZLE_SEND_PREVIEW_USER_EMAIL') || 
                    Configuration::get('ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL')) {
                    $this->sendNotifications($data, $filepath);
                }

                // Pulisci vecchi file temporanei (file più vecchi di 24 ore)
                $this->cleanupTempFiles($upload_dir, 86400); // 86400 secondi = 24 ore

                // Registra il successo nel log
                ArtPuzzleLogger::log('Personalizzazione puzzle salvata con successo. ID: ' . $customization_id);

                // Restituisci successo
                $this->returnResponse(true, 'Personalizzazione salvata con successo', [
                    'idCustomization' => $customization_id,
                    'idProductAttribute' => 0, // 0 per i prodotti senza attributi
                    'filename' => $filename
                ]);
        });
    }
    
    /**
     * Gestisce il controllo della qualità dell'immagine
     */
    protected function handleCheckImageQuality()
    {
        $this->executeAjax(function () {
                $image_data = Tools::getValue('imageData');
                $image_parts = explode(";base64,", $image_data);
                $image_base64 = isset($image_parts[1]) ? $image_parts[1] : null;

                if (!$image_base64) {
                    $this->returnResponse(false, 'Formato immagine non valido');
                    return;
                }

                $image_decoded = base64_decode($image_base64);

                // Verifica che sia un'immagine valida
                $img = @imagecreatefromstring($image_decoded);
                if (!$img) {
                    $this->returnResponse(false, 'Immagine non valida');
                    return;
                }

                // Ottieni dimensioni
                $width = imagesx($img);
                $height = imagesy($img);

                // Valuta qualità
                $quality = 'alta';
                $message = 'L\'immagine è di ottima qualità!';

                // Se l'immagine è troppo piccola, avvisa l'utente
                if ($width < 800 || $height < 800) {
                    $quality = 'bassa';
                    $message = 'L\'immagine è di bassa risoluzione. Potrebbe apparire pixelata sul puzzle.';
                } else if ($width < 1200 || $height < 1200) {
                    $quality = 'media';
                    $message = 'L\'immagine è di media risoluzione. La qualità dovrebbe essere accettabile.';
                }

                imagedestroy($img);

                $this->returnResponse(true, $message, [
                    'quality' => $quality,
                    'width' => $width,
                    'height' => $height
                ]);
        });
    }
    
    /**
     * Restituisce i colori disponibili per la scatola
     */
    protected function handleGetBoxColors()
    {
        $this->executeAjax(function () {
                $box_colors = Configuration::get('ART_PUZZLE_BOX_COLORS');
                $colors_array = json_decode($box_colors, true) ?: [];

                $this->returnResponse(true, 'Colori caricati con successo', $colors_array);
        });
    }
    
    /**
     * Restituisce i font disponibili
     */
    protected function handleGetFonts()
    {
        $this->executeAjax(function () {
                $fonts = Configuration::get('ART_PUZZLE_FONTS');
                $fonts_array = $fonts ? explode(',', $fonts) : [];

                $this->returnResponse(true, 'Font caricati con successo', $fonts_array);
        });
    }
    
    /**
     * Verifica i permessi delle directory
     */
    protected function handleCheckDirectoryPermissions()
    {
        $this->executeAjax(function () {
                $result = $this->checkDirectoryPermissions();

                if ($result['is_valid']) {
                    $this->returnResponse(true, 'Tutte le directory sono scrivibili', [
                        'directories' => $result['directories'],
                    ]);
                } else {
                    $this->returnResponse(false, implode('; ', $result['errors']), [
                        'directories' => $result['directories'],
                    ]);
                }
        });
    }

    /**
     * Verifica lo stato delle directory necessarie al modulo
     *
     * @return array
     */
    protected function checkDirectoryPermissions()
    {
        $directories = [
            'upload' => _PS_MODULE_DIR_.'art_puzzle/upload/',
            'logs' => _PS_MODULE_DIR_.'art_puzzle/logs/',
            'fonts' => _PS_MODULE_DIR_.'art_puzzle/views/fonts/',
        ];

        $status = [
            'is_valid' => true,
            'directories' => [],
            'errors' => [],
        ];

        foreach ($directories as $name => $path) {
            $directoryStatus = [
                'path' => $path,
                'exists' => file_exists($path),
                'is_writable' => false,
                'created' => false,
            ];

            if (!$directoryStatus['exists']) {
                if (@mkdir($path, 0755, true)) {
                    $directoryStatus['exists'] = true;
                    $directoryStatus['created'] = true;
                } else {
                    $status['is_valid'] = false;
                    $directoryStatus['error'] = "Impossibile creare la directory '$name': $path";
                    $status['errors'][] = $directoryStatus['error'];
                }
            }

            if ($directoryStatus['exists']) {
                $directoryStatus['is_writable'] = is_writable($path);
                if (!$directoryStatus['is_writable']) {
                    $status['is_valid'] = false;
                    $directoryStatus['error'] = "La directory '$name' non è scrivibile: $path";
                    $status['errors'][] = $directoryStatus['error'];
                }
            }

            $status['directories'][$name] = $directoryStatus;
        }

        return $status;
    }
    
    /**
     * Crea i campi di personalizzazione per un prodotto
     */
    protected function createCustomizationFields($product_id)
    {
        // Crea campo per l'immagine
        Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'customization_field` 
            (`id_product`, `type`, `required`) 
            VALUES ('.(int)$product_id.', 0, 0)'
        );
        
        $id_field_image = Db::getInstance()->Insert_ID();
        
        // Aggiungi label per tutte le lingue
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'customization_field_lang` 
                (`id_customization_field`, `id_lang`, `name`) 
                VALUES (
                    '.(int)$id_field_image.', 
                    '.(int)$language['id_lang'].', 
                    \'Immagine Puzzle\'
                )
            ');
        }
        
        // Crea campo per i dettagli della scatola
        Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'customization_field` 
            (`id_product`, `type`, `required`) 
            VALUES ('.(int)$product_id.', 1, 0)'
        );
        
        $id_field_box = Db::getInstance()->Insert_ID();
        
        // Aggiungi label per tutte le lingue
        foreach ($languages as $language) {
            Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'customization_field_lang` 
                (`id_customization_field`, `id_lang`, `name`) 
                VALUES (
                    '.(int)$id_field_box.', 
                    '.(int)$language['id_lang'].', 
                    \'Dettagli Scatola\'
                )
            ');
        }
        
        // Imposta il prodotto come personalizzabile
        $product = new Product($product_id);
        $product->customizable = 1;
        $product->uploadable_files = 1;
        $product->text_fields = 1;
        $product->save();
    }
    
    /**
     * Ottieni o crea un ID personalizzazione
     */
    protected function getOrCreateCustomization($id_cart, $id_product)
    {
        $id_customization = null;
        
        // Controlla se esiste già una personalizzazione per questo prodotto nel carrello
        $result = Db::getInstance()->getRow('
            SELECT `id_customization` 
            FROM `'._DB_PREFIX_.'customization` 
            WHERE `id_cart` = '.(int)$id_cart.' 
            AND `id_product` = '.(int)$id_product.'
            AND `in_cart` = 0
        ');
        
        if ($result && isset($result['id_customization'])) {
            $id_customization = (int)$result['id_customization'];
        } else {
            // Crea una nuova personalizzazione
            Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'customization` 
                (`id_cart`, `id_product`, `id_product_attribute`, `quantity`, `in_cart`) 
                VALUES (
                    '.(int)$id_cart.', 
                    '.(int)$id_product.', 
                    0, 
                    0, 
                    0
                )
            ');
            
            $id_customization = Db::getInstance()->Insert_ID();
        }
        
        return $id_customization;
    }
    
    /**
     * Salva la personalizzazione di tipo file
     */
    protected function saveFileCustomization($id_customization, $id_customization_field, $filepath, $filename)
    {
        // Controlla se esiste già una personalizzazione per questo campo
        $exists = Db::getInstance()->getValue('
            SELECT COUNT(*) 
            FROM `'._DB_PREFIX_.'customized_data` 
            WHERE `id_customization` = '.(int)$id_customization.' 
            AND `type` = 0 
            AND `index` = '.(int)$id_customization_field
        );
        
        if ($exists) {
            // Aggiorna la personalizzazione esistente
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'customized_data` 
                SET `value` = \''.pSQL($filename).'\' 
                WHERE `id_customization` = '.(int)$id_customization.' 
                AND `type` = 0 
                AND `index` = '.(int)$id_customization_field
            );
        } else {
            // Crea una nuova personalizzazione
            Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'customized_data` 
                (`id_customization`, `type`, `index`, `value`) 
                VALUES (
                    '.(int)$id_customization.', 
                    0, 
                    '.(int)$id_customization_field.', 
                    \''.pSQL($filename).'\'
                )
            ');
        }
    }
    
    /**
     * Salva la personalizzazione di tipo testo
     */
    protected function saveTextCustomization($id_customization, $id_customization_field, $value)
    {
        // Controlla se esiste già una personalizzazione per questo campo
        $exists = Db::getInstance()->getValue('
            SELECT COUNT(*) 
            FROM `'._DB_PREFIX_.'customized_data` 
            WHERE `id_customization` = '.(int)$id_customization.' 
            AND `type` = 1 
            AND `index` = '.(int)$id_customization_field
        );
        
        if ($exists) {
            // Aggiorna la personalizzazione esistente
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'customized_data` 
                SET `value` = \''.pSQL($value).'\' 
                WHERE `id_customization` = '.(int)$id_customization.' 
                AND `type` = 1 
                AND `index` = '.(int)$id_customization_field
            );
        } else {
            // Crea una nuova personalizzazione
            Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'customized_data` 
                (`id_customization`, `type`, `index`, `value`) 
                VALUES (
                    '.(int)$id_customization.', 
                    1, 
                    '.(int)$id_customization_field.', 
                    \''.pSQL($value).'\'
                )
            ');
        }
    }
    
    /**
     * Invia le email di notifica
     */
    protected function sendNotifications($data, $imagePath)
    {
        $product = new Product($data['product_id'], false, $this->context->language->id);
        
        // Prepara dati comuni
        $templateVars = [
            '{product_name}' => $product->name,
            '{box_text}' => $data['customization']['boxText'],
            '{box_color}' => $data['customization']['boxColor'],
            '{text_color}' => $data['customization']['textColor'],
            '{font}' => $data['customization']['font'],
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => $this->context->link->getBaseLink(),
            '{shop_logo}' => _PS_IMG_DIR_ . Configuration::get('PS_LOGO')
        ];
        
        // Email all'utente
        if (Configuration::get('ART_PUZZLE_SEND_PREVIEW_USER_EMAIL') && $this->context->customer->id) {
            $customer = new Customer($this->context->customer->id);
            
            // Estendi template con valori specifici per il cliente
            $userTemplateVars = array_merge($templateVars, [
                '{my_account_url}' => $this->context->link->getPageLink('my-account'),
                '{history_url}' => $this->context->link->getPageLink('history')
            ]);
            
            // Allega l'immagine se abilitato
            $fileAttachment = null;
            if (Configuration::get('ART_PUZZLE_ENABLE_PDF_USER') && file_exists($imagePath)) {
                // Crea PDF se abilitato
                $pdfPath = _PS_MODULE_DIR_ . 'art_puzzle/upload/pdf_' . time() . '_' . Tools::passwdGen(8) . '.pdf';
                PDFGeneratorPuzzle::generateClientPDF($imagePath, $customer->firstname . ' ' . $customer->lastname, $pdfPath);
                
                $fileAttachment = [
                    'content' => file_get_contents($pdfPath),
                    'name' => 'puzzle_personalizzato.pdf',
                    'mime' => 'application/pdf'
                ];
                
                // Pulisci il PDF temporaneo
                @unlink($pdfPath);
            }
            
            Mail::Send(
                (int)$this->context->language->id,
                'art_puzzle_user',
                'La tua personalizzazione del puzzle',
                $userTemplateVars,
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                null,
                null,
                $fileAttachment,
                null,
                _PS_MODULE_DIR_ . 'art_puzzle/mails/',
                false,
                (int)$this->context->shop->id
            );
        }
        
        // Email all'admin
        if (Configuration::get('ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL')) {
            $adminEmail = Configuration::get('ART_PUZZLE_ADMIN_EMAIL');
            
            if (!empty($adminEmail)) {
                // Aggiungi info cliente per l'admin
                $adminTemplateVars = array_merge($templateVars, []);
                
                if ($this->context->customer->id) {
                    $customer = new Customer($this->context->customer->id);
                    $adminTemplateVars['{customer_name}'] = $customer->firstname . ' ' . $customer->lastname;
                    $adminTemplateVars['{customer_email}'] = $customer->email;
                } else {
                    $adminTemplateVars['{customer_name}'] = 'Visitatore';
                    $adminTemplateVars['{customer_email}'] = 'N/A';
                }
                
                // Allega l'immagine o PDF
                $fileAttachment = null;
                if (Configuration::get('ART_PUZZLE_ENABLE_PDF_ADMIN') && file_exists($imagePath)) {
                    // Crea PDF se abilitato
                    $pdfPath = _PS_MODULE_DIR_ . 'art_puzzle/upload/pdf_admin_' . time() . '_' . Tools::passwdGen(8) . '.pdf';
                    $boxImagePath = ""; // In una versione completa, qui andrebbe generata l'immagine della scatola
                    PDFGeneratorPuzzle::generateAdminPDF($imagePath, $boxImagePath, $data['customization']['boxText'], $pdfPath);
                    
                    $fileAttachment = [
                        'content' => file_get_contents($pdfPath),
                        'name' => 'puzzle_personalizzato_admin.pdf',
                        'mime' => 'application/pdf'
                    ];
                    
                    // Pulisci il PDF temporaneo
                    @unlink($pdfPath);
                } elseif (file_exists($imagePath)) {
                    $fileAttachment = [
                        'content' => file_get_contents($imagePath),
                        'name' => 'puzzle_preview.png',
                        'mime' => 'image/png'
                    ];
                }
                
                Mail::Send(
                    (int)$this->context->language->id,
                    'art_puzzle_admin',
                    'Nuova personalizzazione puzzle',
                    $adminTemplateVars,
                    $adminEmail,
                    'Amministratore',
                    null,
                    null,
                    $fileAttachment,
                    null,
                    _PS_MODULE_DIR_ . 'art_puzzle/mails/',
                    false,
                    (int)$this->context->shop->id
                );
            }
        }
    }
    
    /**
     * Pulisce i file temporanei
     */
    protected function cleanupTempFiles($directory, $maxAge)
    {
        if (!is_dir($directory)) {
            return;
        }
        
        $now = time();
        $files = scandir($directory);
        
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || $file == 'index.php') {
                continue;
            }
            
            $filePath = $directory . $file;
            if (is_file($filePath)) {
                // Se il file è più vecchio del tempo massimo, eliminalo
                if ($now - filemtime($filePath) > $maxAge) {
                    @unlink($filePath);
                    
                    ArtPuzzleLogger::log('File temporaneo eliminato: ' . $file);
                }
            }
        }
    }
}
