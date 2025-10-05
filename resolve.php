<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Rende visibili i binari necessari ai comandi shell lanciati dallo script
putenv('PATH=/opt/im6/bin:/opt/php-5.3.29/bin:' . getenv('PATH'));
putenv('LD_LIBRARY_PATH=/opt/curl-php53/lib:/opt/openssl-1.0.2u/lib:/opt/libmcrypt/lib');

/**
 * RISOLVE.PHP - Sostituzione Automatica Imagick con ImageMagick Command Line
 * Compatibile PHP 5.3.29 - Risolve tutti i problemi Imagick nel sistema
 */

// --- POLYFILL IMAGEMAGICK CLI ---
if (!function_exists('im_new_image')) {
    function im_new_image($width, $height, $bgColor, $outputPath) {
        // Assicura che i binari siano nel PATH
        $imPath = '/opt/im6/bin';
        $currentPath = getenv('PATH');
        if (strpos($currentPath, $imPath) === false) {
            putenv('PATH=' . $imPath . ':' . $currentPath);
        }

        // Verifica che 'convert' esista
        $convert = trim(shell_exec('command -v convert'));
        if ($convert === '') {
            return false;
        }

        // Crea immagine WxH con colore di sfondo
        $w = (int)$width;
        $h = (int)$height;
        if ($w <= 0 || $h <= 0) return false;

        $cmd = sprintf(
            '%s -size %dx%d xc:%s %s 2>&1',
            escapeshellcmd($convert),
            $w,
            $h,
            escapeshellarg($bgColor),
            escapeshellarg($outputPath)
        );

        exec($cmd, $out, $ret);
        return ($ret === 0) && is_file($outputPath);
    }
}
// --- FINE POLYFILL ---

// Attiva tutti gli errori
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurazione
$DRY_RUN = false; // Cambia in true per testare senza modificare i file
$CREATE_BACKUP = true; // Crea backup automatico
$TEST_COMMANDS = true; // Testa i comandi ImageMagick

// File da processare (dall'analisi precedente)
$files_to_process = array(
    './assets/classes/imagetools.php',
    './assets/includes/photo.puzzle.func.php', 
    './assets/includes/photo.puzzle.inc.php',
    './assets/includes/shared.functions.php',
    './manager/mgr.functions.bu-w-imagetools.php',
    './manager/mgr.image.preview.php',
    './manager/mgr.orders.edit.php'
);

$log = array();
$errors = array();
$successes = array();

function add_log($message, $type = 'INFO') {
    global $log;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$type] $message";
    $log[] = $log_entry;
    echo $log_entry . "\n";
}

function create_backup($file) {
    $backup_file = $file . '.backup.' . date('Ymd_His');
    if (copy($file, $backup_file)) {
        add_log("Backup creato: $backup_file", 'SUCCESS');
        return $backup_file;
    } else {
        add_log("ERRORE: Impossibile creare backup per $file", 'ERROR');
        return false;
    }
}

function test_imagemagick() {
    add_log("Test disponibilità ImageMagick...");
    
    $commands = array('convert', 'identify', 'composite');
    $available = true;
    
    foreach ($commands as $cmd) {
        $output = array();
        $return_code = 0;
        exec("which $cmd 2>/dev/null", $output, $return_code);
        
        if ($return_code !== 0) {
            add_log("ERRORE: Comando '$cmd' non disponibile", 'ERROR');
            $available = false;
        } else {
            add_log("OK: $cmd disponibile in " . implode('', $output), 'SUCCESS');
        }
    }
    
    return $available;
}

/**
 * Funzioni di utilità per la conversione
 */
function generate_imagemagick_functions() {
    return '
/**
 * FUNZIONI IMAGEMAGICK COMMAND LINE - Auto-generate da risolve.php
 */

function im_get_image_geometry($file_path) {
    $cmd = "identify -format \'%wx%h\' " . escapeshellarg($file_path) . " 2>&1";
    $output = array();
    exec($cmd, $output, $return_code);
    
    if ($return_code === 0 && !empty($output[0])) {
        list($width, $height) = explode(\'x\', $output[0]);
        return array(\'width\' => (int)$width, \'height\' => (int)$height);
    }
    return false;
}

function im_get_image_format($file_path) {
    $cmd = "identify -format \'%m\' " . escapeshellarg($file_path) . " 2>&1";
    $output = array();
    exec($cmd, $output, $return_code);
    
    if ($return_code === 0 && !empty($output[0])) {
        return trim($output[0]);
    }
    return false;
}

function im_scale_image($input_file, $output_file, $width, $height = 0, $format = null) {
    $size_param = $height > 0 ? $width . "x" . $height : $width . "x";
    $cmd = "convert " . escapeshellarg($input_file) . " -resize " . escapeshellarg($size_param) . " ";
    
    if ($format) {
        $cmd .= "-format " . escapeshellarg(strtolower($format)) . " ";
    }
    
    $cmd .= escapeshellarg($output_file) . " 2>&1";
    
    $output = array();
    exec($cmd, $output, $return_code);
    
    return $return_code === 0;
}

function im_crop_image($input_file, $output_file, $width, $height, $x, $y) {
    $crop_param = $width . "x" . $height . "+" . $x . "+" . $y;
    $cmd = "convert " . escapeshellarg($input_file) . " -crop " . escapeshellarg($crop_param) . " +repage " . escapeshellarg($output_file) . " 2>&1";
    
    $output = array();
    exec($cmd, $output, $return_code);
    
    return $return_code === 0;
}

function im_composite_image($base_file, $overlay_file, $output_file, $x = 0, $y = 0, $compose = "over") {
    $cmd = "composite -compose " . escapeshellarg($compose) . " -geometry +" . $x . "+" . $y . " " . 
           escapeshellarg($overlay_file) . " " . escapeshellarg($base_file) . " " . escapeshellarg($output_file) . " 2>&1";
    
    $output = array();
    exec($cmd, $output, $return_code);
    
    return $return_code === 0;
}

function im_set_image_format($input_file, $output_file, $format) {
    $cmd = "convert " . escapeshellarg($input_file) . " -format " . escapeshellarg(strtolower($format)) . " " . escapeshellarg($output_file) . " 2>&1";
    
    $output = array();
    exec($cmd, $output, $return_code);
    
    return $return_code === 0;
}

function im_new_image($width, $height, $color, $output_file, $format = "png") {
    $size_param = $width . "x" . $height;
    $cmd = "convert -size " . escapeshellarg($size_param) . " xc:" . escapeshellarg($color) . " -format " . escapeshellarg($format) . " " . escapeshellarg($output_file) . " 2>&1";
    
    $output = array();
    exec($cmd, $output, $return_code);
    
    return $return_code === 0;
}

function im_read_image($file_path) {
    // Per command line, "leggere" significa solo verificare che esista
    return file_exists($file_path);
}

function im_write_image($temp_file, $output_file) {
    if (file_exists($temp_file)) {
        return copy($temp_file, $output_file);
    }
    return false;
}

function im_clean_temp_files($pattern) {
    $files = glob($pattern);
    if ($files) {
        foreach ($files as $file) {
            @unlink($file);
        }
    }
}

';
}

/**
 * Patterns di sostituzione
 */
function get_replacement_patterns() {
    return array(
        // 1. new Imagick() -> setup variabili
        array(
            'pattern' => '/\$(\w+)\s*=\s*new\s+Imagick\s*\(\s*([^)]*)\s*\)\s*;/i',
            'replacement' => '// $1 = new Imagick($2); // Converted to ImageMagick CLI
$1_file = $2;
$1_temp_files = array();'
        ),
        
        // 2. getImageGeometry() -> im_get_image_geometry()
        array(
            'pattern' => '/\$(\w+)\s*=\s*\$(\w+)->getImageGeometry\(\s*\)\s*;/i',
            'replacement' => '$1 = im_get_image_geometry($2_file);'
        ),
        
        // 3. getImageFormat() -> im_get_image_format()
        array(
            'pattern' => '/\$(\w+)\s*=\s*\$(\w+)->getImageFormat\(\s*\)\s*;/i',
            'replacement' => '$1 = im_get_image_format($2_file);'
        ),
        
        // 4. readImage() -> im_read_image()
        array(
            'pattern' => '/\$(\w+)->readImage\s*\(\s*([^)]+)\s*\)\s*;/i',
            'replacement' => '$1_file = $2;
if (!im_read_image($1_file)) { 
    add_log("Errore lettura immagine: " . $1_file, "ERROR"); 
}'
        ),
        
        // 5. scaleImage() -> im_scale_image()
        array(
            'pattern' => '/\$(\w+)->scaleImage\s*\(\s*([^,]+)\s*,\s*([^)]+)\s*\)\s*;/i',
            'replacement' => '$1_temp_file = tempnam(sys_get_temp_dir(), "im_scale_") . ".png";
$1_temp_files[] = $1_temp_file;
if (im_scale_image($1_file, $1_temp_file, $2, $3)) {
    $1_file = $1_temp_file;
} else {
    add_log("Errore scale immagine", "ERROR");
}'
        ),
        
        // 6. cropImage() -> im_crop_image()
        array(
            'pattern' => '/\$(\w+)->cropImage\s*\(\s*([^,]+)\s*,\s*([^,]+)\s*,\s*([^,]+)\s*,\s*([^)]+)\s*\)\s*;/i',
            'replacement' => '$1_temp_file = tempnam(sys_get_temp_dir(), "im_crop_") . ".png";
$1_temp_files[] = $1_temp_file;
if (im_crop_image($1_file, $1_temp_file, $2, $3, $4, $5)) {
    $1_file = $1_temp_file;
} else {
    add_log("Errore crop immagine", "ERROR");
}'
        ),
        
        // 7. setImageFormat() -> variabile formato
        array(
            'pattern' => '/\$(\w+)->setImageFormat\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*;/i',
            'replacement' => '$1_format = "$2";'
        ),
        
        // 8. writeImage() -> im_write_image() o copy finale
        array(
            'pattern' => '/\$(\w+)->writeImage\s*\(\s*([^)]+)\s*\)\s*;/i',
            'replacement' => 'if (isset($1_format) && $1_format) {
    $1_final_temp = tempnam(sys_get_temp_dir(), "im_final_") . "." . strtolower($1_format);
    if (im_set_image_format($1_file, $1_final_temp, $1_format)) {
        copy($1_final_temp, $2);
        @unlink($1_final_temp);
    }
} else {
    copy($1_file, $2);
}
// Cleanup temp files
if (isset($1_temp_files)) {
    foreach ($1_temp_files as $temp_file) {
        @unlink($temp_file);
    }
}'
        ),
        
        // 9. destroy() / clear() -> cleanup
        array(
            'pattern' => '/\$(\w+)->(destroy|clear)\s*\(\s*\)\s*;/i',
            'replacement' => '// $1->$2(); // Cleanup gestito automaticamente
if (isset($1_temp_files)) {
    foreach ($1_temp_files as $temp_file) {
        @unlink($temp_file);
    }
    unset($1_temp_files);
}'
        ),
        
        // 10. getImageBlob() -> file_get_contents del file temporaneo
        array(
            'pattern' => '/\$(\w+)\s*=\s*\$(\w+)->getImageBlob\(\s*\)\s*;/i',
            'replacement' => '$1 = file_get_contents($2_file);'
        ),
        
        // 11. Compositing complesso -> comando composite
        array(
            'pattern' => '/\$(\w+)->compositeImage\s*\(\s*\$(\w+)\s*,\s*[^,]+\s*,\s*([^,]+)\s*,\s*([^)]+)\s*[^)]*\)\s*;/i',
            'replacement' => '$1_comp_temp = tempnam(sys_get_temp_dir(), "im_comp_") . ".png";
$1_temp_files[] = $1_comp_temp;
if (im_composite_image($1_file, $2_file, $1_comp_temp, $3, $4)) {
    $1_file = $1_comp_temp;
} else {
    add_log("Errore composite immagine", "ERROR");
}'
        )
    );
}

/**
 * Sostituzioni specifiche per funzioni complesse
 */
function get_complex_replacements() {
    return array(
        // Fix per la funzione getRestore in photo.puzzle.func.php
        'getRestore_fix' => array(
            'search' => '$im = new Imagick($newPath.$img);
	$aPixelSize = $im->getImageGeometry();
	$imageWidth = $aPixelSize[\'width\'];
	$imageHeight = $aPixelSize[\'height\'];',
            'replace' => '// $im = new Imagick($newPath.$img); // Converted to ImageMagick CLI
	$aPixelSize = im_get_image_geometry($newPath.$img);
	if ($aPixelSize) {
		$imageWidth = $aPixelSize[\'width\'];
		$imageHeight = $aPixelSize[\'height\'];
	} else {
		add_log("Errore lettura dimensioni immagine: " . $newPath.$img, "ERROR");
		return json_encode(array(\'res\' => \'ko\', \'detail\' => \'errore lettura immagine\'));
	}'
        ),
        
        // Fix per upload in photo.puzzle.inc.php
        'upload_fix' => array(
            'search' => '$im = new Imagick($_FILES["file"]["tmp_name"]);',
            'replace' => '// $im = new Imagick($_FILES["file"]["tmp_name"]); // Converted to ImageMagick CLI
			$im_file = $_FILES["file"]["tmp_name"];
			$im_temp_files = array();'
        ),
        
        // Fix per imagetools.php
        'imagetools_fix' => array(
            'search' => '$image = new Imagick($src);
				$size[0] = $image->getImageWidth();
				$size[1] = $image->getImageHeight();',
            'replace' => '// $image = new Imagick($src); // Converted to ImageMagick CLI
				$image_geometry = im_get_image_geometry($src);
				if ($image_geometry) {
					$size[0] = $image_geometry[\'width\'];
					$size[1] = $image_geometry[\'height\'];
				} else {
					add_log("Errore lettura dimensioni in imagetools: " . $src, "ERROR");
					return false;
				}'
        )
    );
}

/**
 * Processa un singolo file
 */
function process_file($file_path) {
    global $DRY_RUN, $CREATE_BACKUP, $errors, $successes;
    
    add_log("Processando file: $file_path");
    
    if (!file_exists($file_path)) {
        add_log("ERRORE: File non trovato: $file_path", 'ERROR');
        $errors[] = $file_path;
        return false;
    }
    
    // Leggi il contenuto
    $original_content = file_get_contents($file_path);
    if ($original_content === false) {
        add_log("ERRORE: Impossibile leggere il file: $file_path", 'ERROR');
        $errors[] = $file_path;
        return false;
    }
    
    $modified_content = $original_content;
    $changes_made = 0;
    
    // Aggiungi le funzioni helper all'inizio del file se non esistono
    if (strpos($modified_content, 'im_get_image_geometry') === false) {
        $functions = generate_imagemagick_functions();
        // Inserisci dopo l'apertura <?php
        $modified_content = preg_replace('/(<\?php\s*)/', '$1' . $functions, $modified_content, 1);
        $changes_made++;
        add_log("Aggiunte funzioni ImageMagick helper", 'INFO');
    }
    
    // Applica i pattern di sostituzione
    $patterns = get_replacement_patterns();
    foreach ($patterns as $pattern_info) {
        $pattern = $pattern_info['pattern'];
        $replacement = $pattern_info['replacement'];
        
        $count = 0;
        $modified_content = preg_replace($pattern, $replacement, $modified_content, -1, $count);
        
        if ($count > 0) {
            $changes_made += $count;
            add_log("Pattern applicato $count volte: " . substr($pattern, 0, 50) . "...", 'INFO');
        }
    }
    
    // Applica sostituzioni complesse specifiche
    $complex_replacements = get_complex_replacements();
    foreach ($complex_replacements as $name => $replacement) {
        if (strpos($modified_content, $replacement['search']) !== false) {
            $modified_content = str_replace($replacement['search'], $replacement['replace'], $modified_content);
            $changes_made++;
            add_log("Applicata sostituzione complessa: $name", 'INFO');
        }
    }
    
    // Controlla se ci sono ancora pattern Imagick non convertiti
    $remaining_patterns = array(
        'new Imagick',
        '->getImageGeometry',
        '->scaleImage',
        '->cropImage',
        '->writeImage',
        '->readImage',
        '->setImageFormat',
        '->getImageBlob',
        '->compositeImage',
        '->destroy',
        '->clear'
    );
    
    foreach ($remaining_patterns as $pattern) {
        if (strpos($modified_content, $pattern) !== false) {
            add_log("ATTENZIONE: Pattern non convertito trovato: $pattern", 'WARNING');
        }
    }
    
    if ($changes_made > 0) {
        add_log("Totale modifiche applicate: $changes_made", 'SUCCESS');
        
        if (!$DRY_RUN) {
            // Crea backup se richiesto
            if ($CREATE_BACKUP) {
                if (!create_backup($file_path)) {
                    $errors[] = $file_path;
                    return false;
                }
            }
            
            // Scrivi il file modificato
            if (file_put_contents($file_path, $modified_content) === false) {
                add_log("ERRORE: Impossibile scrivere il file: $file_path", 'ERROR');
                $errors[] = $file_path;
                return false;
            }
            
            add_log("File salvato con successo: $file_path", 'SUCCESS');
        } else {
            add_log("DRY RUN: File NON modificato: $file_path", 'INFO');
        }
        
        $successes[] = $file_path;
        return true;
    } else {
        add_log("Nessuna modifica necessaria per: $file_path", 'INFO');
        return true;
    }
}

/**
 * Test finale
 */
function run_final_tests() {
    global $files_to_process, $TEST_COMMANDS;
    
    if (!$TEST_COMMANDS) {
        add_log("Test comandi saltati (TEST_COMMANDS = false)", 'INFO');
        return;
    }
    
    add_log("Esecuzione test finale...", 'INFO');
    
    // Test creazione immagine semplice
    $test_file = sys_get_temp_dir() . '/test_imagemagick_' . time() . '.png';
    if (im_new_image(100, 100, 'blue', $test_file)) {
        add_log("Test creazione immagine: SUCCESS", 'SUCCESS');
        @unlink($test_file);
    } else {
        add_log("Test creazione immagine: FALLITO", 'ERROR');
    }
    
    // Test identify su un file esistente
    foreach ($files_to_process as $file) {
        if (file_exists($file)) {
            $syntax_check = exec("php -l \"$file\" 2>&1", $output, $return_code);
            if ($return_code === 0) {
                add_log("Sintassi PHP OK: $file", 'SUCCESS');
            } else {
                add_log("ERRORE SINTASSI PHP: $file - " . implode(' ', $output), 'ERROR');
            }
        }
    }
}

/**
 * Main execution
 */
function main() {
    global $files_to_process, $DRY_RUN, $log, $errors, $successes;
    
    add_log("=== INIZIO CONVERSIONE IMAGICK -> IMAGEMAGICK CLI ===");
    add_log("Modalità: " . ($DRY_RUN ? "DRY RUN (nessuna modifica)" : "MODIFICA FILES"));
    
    // Test disponibilità ImageMagick
    if (!test_imagemagick()) {
        add_log("ERRORE CRITICO: ImageMagick non disponibile. Installare prima di procedere.", 'ERROR');
        return false;
    }
    
    // Processa ogni file
    foreach ($files_to_process as $file) {
        process_file($file);
    }
    
    // Test finale
    run_final_tests();
    
    // Report finale
    add_log("=== REPORT FINALE ===");
    add_log("File processati con successo: " . count($successes));
    add_log("File con errori: " . count($errors));
    
    if (count($errors) > 0) {
        add_log("File con errori:");
        foreach ($errors as $error_file) {
            add_log("  - $error_file");
        }
    }
    
    if (count($successes) > 0) {
        add_log("File convertiti:");
        foreach ($successes as $success_file) {
            add_log("  - $success_file");
        }
    }
    
    // Salva log
    $log_content = implode("\n", $log);
    $log_file = 'risolve_log_' . date('Ymd_His') . '.txt';
    if (file_put_contents($log_file, $log_content) !== false) {
        add_log("Log salvato in: $log_file", 'SUCCESS');
    }
    
    add_log("=== CONVERSIONE COMPLETATA ===");
    
    return count($errors) === 0;
}

// Esegui lo script
if (php_sapi_name() === 'cli') {
    // Eseguito da command line
    main();
} else {
    // Eseguito da web browser
    echo "<pre>";
    main();
    echo "</pre>";
}

?>