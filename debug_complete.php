<?php
/**
 * DEBUG COMPLETO SISTEMA REGISTRAZIONE
 * Salvare come debug_registration_complete.php nella root del sito
 * Accedere via browser: https://fotoartpuzzle.it/debug_registration_complete.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$debug_results = [];
$errors_found = [];
$solutions = [];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Sistema Registrazione - FotoArtPuzzle</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #d32f2f; text-align: center; }
        h2 { color: #1976d2; border-bottom: 2px solid #1976d2; padding-bottom: 5px; }
        .success { color: #388e3c; font-weight: bold; }
        .error { color: #d32f2f; font-weight: bold; }
        .warning { color: #f57c00; font-weight: bold; }
        .solution { background: #e8f5e8; padding: 10px; margin: 10px 0; border-left: 4px solid #4caf50; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { padding: 10px 15px; margin: 5px; background: #1976d2; color: white; border: none; cursor: pointer; border-radius: 4px; }
        .btn:hover { background: #1565c0; }
        .result-box { margin: 10px 0; padding: 10px; border: 1px solid #ddd; background: #f9f9f9; min-height: 30px; border-radius: 4px; }
        code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
        .critical { background: #ffebee; border-left: 4px solid #f44336; padding: 10px; margin: 10px 0; }
        .copy-code { background: #263238; color: #fff; padding: 10px; border-radius: 4px; font-family: monospace; white-space: pre-wrap; margin: 10px 0; overflow-x: auto; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tabs { border-bottom: 1px solid #ddd; margin-bottom: 20px; }
        .tab { display: inline-block; padding: 10px 20px; cursor: pointer; border-bottom: 2px solid transparent; }
        .tab.active { border-bottom-color: #1976d2; color: #1976d2; }
        .progress { width: 100%; background: #f0f0f0; border-radius: 10px; margin: 10px 0; }
        .progress-bar { height: 20px; background: #4caf50; border-radius: 10px; text-align: center; color: white; line-height: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 DEBUG SISTEMA REGISTRAZIONE</h1>
    <p><strong>Sito:</strong> fotoartpuzzle.it | <strong>Data:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    
    <div class="tabs">
        <div class="tab active" onclick="showTab('system')">Sistema Base</div>
        <div class="tab" onclick="showTab('database')">Database</div>
        <div class="tab" onclick="showTab('javascript')">JavaScript</div>
        <div class="tab" onclick="showTab('form')">Test Form</div>
        <div class="tab" onclick="showTab('solutions')">Soluzioni</div>
    </div>

    <!-- TAB SISTEMA BASE -->
    <div id="system" class="tab-content active">
        <h2>1. VERIFICA SISTEMA BASE</h2>
        
        <?php
        // Verifica file essenziali
        echo "<div class='test-section'>";
        echo "<h3>File Essenziali</h3>";
        
        $files_to_check = [
            'actions.php' => 'File azioni server-side',
            'create.account.php' => 'Pagina registrazione',
            'assets/javascript/create.account.js' => 'JavaScript registrazione',
            'assets/javascript/public.min.js' => 'JavaScript pubblico',
            'assets/includes/session.php' => 'Gestione sessioni',
            'assets/includes/initialize.php' => 'Inizializzazione sistema'
        ];
        
        $files_ok = 0;
        foreach($files_to_check as $file => $desc) {
            if(file_exists($file)) {
                echo "<span class='success'>✅ $file</span> - $desc<br>";
                $files_ok++;
            } else {
                echo "<span class='error'>❌ $file</span> - $desc - <strong>FILE MANCANTE</strong><br>";
                $errors_found[] = "File mancante: $file";
            }
        }
        
        $file_percentage = round(($files_ok / count($files_to_check)) * 100);
        echo "<div class='progress'><div class='progress-bar' style='width: {$file_percentage}%'>{$file_percentage}% File OK</div></div>";
        echo "</div>";
        
        // Verifica permessi
        echo "<div class='test-section'>";
        echo "<h3>Permessi Directory</h3>";
        $dirs_to_check = ['.', 'assets', 'assets/javascript', 'assets/includes'];
        foreach($dirs_to_check as $dir) {
            if(is_readable($dir)) {
                echo "<span class='success'>✅ $dir</span> - Leggibile<br>";
            } else {
                echo "<span class='error'>❌ $dir</span> - Non leggibile<br>";
                $errors_found[] = "Permessi directory: $dir non leggibile";
            }
        }
        echo "</div>";
        
        // Verifica PHP
        echo "<div class='test-section'>";
        echo "<h3>Configurazione PHP</h3>";
        echo "PHP Version: <strong>" . phpversion() . "</strong><br>";
        echo "Error Reporting: <strong>" . (error_reporting() ? 'ON' : 'OFF') . "</strong><br>";
        echo "Display Errors: <strong>" . (ini_get('display_errors') ? 'ON' : 'OFF') . "</strong><br>";
        
        $required_extensions = ['mysqli', 'json', 'session'];
        foreach($required_extensions as $ext) {
            if(extension_loaded($ext)) {
                echo "<span class='success'>✅ $ext</span> extension<br>";
            } else {
                echo "<span class='error'>❌ $ext</span> extension<br>";
                $errors_found[] = "Estensione PHP mancante: $ext";
            }
        }
        echo "</div>";
        ?>
    </div>

    <!-- TAB DATABASE -->
    <div id="database" class="tab-content">
        <h2>2. VERIFICA DATABASE</h2>
        
        <?php
        echo "<div class='test-section'>";
        echo "<h3>Connessione Database</h3>";
        
        try {
            define('BASE_PATH', dirname(__FILE__));
            
            // Prova a includere i file necessari
            $db_connected = false;
            if(file_exists(BASE_PATH.'/assets/includes/session.php')) {
                require_once BASE_PATH.'/assets/includes/session.php';
            }
            if(file_exists(BASE_PATH.'/assets/includes/initialize.php')) {
                require_once BASE_PATH.'/assets/includes/initialize.php';
            }
            
            if(isset($db) && $db) {
                echo "<span class='success'>✅ Connessione database stabilita</span><br>";
                $db_connected = true;
                
                // Test query
                $test_query = mysqli_query($db, "SELECT 1 as test");
                if($test_query) {
                    echo "<span class='success'>✅ Query test eseguita con successo</span><br>";
                } else {
                    echo "<span class='error'>❌ Errore query test: " . mysqli_error($db) . "</span><br>";
                    $errors_found[] = "Errore query database: " . mysqli_error($db);
                }
                
                // Verifica tabelle
                echo "<h4>Tabelle Necessarie</h4>";
                $tables = ['members', 'countries', 'states', 'memberships', 'registration_form'];
                $tables_ok = 0;
                
                foreach($tables as $table) {
                    $full_table = (isset($dbinfo['pre']) ? $dbinfo['pre'] : '') . $table;
                    $check = mysqli_query($db, "SHOW TABLES LIKE '$full_table'");
                    if($check && mysqli_num_rows($check) > 0) {
                        echo "<span class='success'>✅ $full_table</span><br>";
                        $tables_ok++;
                        
                        // Conta record per verificare dati
                        $count_query = mysqli_query($db, "SELECT COUNT(*) as cnt FROM $full_table");
                        if($count_query) {
                            $count = mysqli_fetch_assoc($count_query)['cnt'];
                            echo "&nbsp;&nbsp;&nbsp;Records: <strong>$count</strong><br>";
                        }
                    } else {
                        echo "<span class='error'>❌ $full_table - TABELLA MANCANTE</span><br>";
                        $errors_found[] = "Tabella database mancante: $full_table";
                    }
                }
                
                $table_percentage = round(($tables_ok / count($tables)) * 100);
                echo "<div class='progress'><div class='progress-bar' style='width: {$table_percentage}%'>{$table_percentage}% Tabelle OK</div></div>";
                
            } else {
                echo "<span class='error'>❌ Impossibile stabilire connessione database</span><br>";
                echo "Variabile \$db non inizializzata o connessione fallita<br>";
                $errors_found[] = "Connessione database fallita";
            }
            
        } catch(Exception $e) {
            echo "<span class='error'>❌ Errore database: " . $e->getMessage() . "</span><br>";
            $errors_found[] = "Eccezione database: " . $e->getMessage();
        }
        echo "</div>";
        
        // Test endpoint actions.php
        echo "<div class='test-section'>";
        echo "<h3>Test Endpoint Stati</h3>";
        echo "<button class='btn' onclick='testStateList()'>Test Caricamento Stati</button>";
        echo "<div id='stateResult' class='result-box'></div>";
        echo "</div>";
        ?>
    </div>

    <!-- TAB JAVASCRIPT -->
    <div id="javascript" class="tab-content">
        <h2>3. VERIFICA JAVASCRIPT</h2>
        
        <div class="test-section">
            <h3>Librerie e Variabili</h3>
            <button class="btn" onclick="testJavaScript()">Verifica JavaScript</button>
            <div id="jsResult" class="result-box"></div>
        </div>
        
        <div class="test-section">
            <h3>Funzioni Necessarie</h3>
            <button class="btn" onclick="testFunctions()">Test Funzioni</button>
            <div id="functionsResult" class="result-box"></div>
        </div>
        
        <div class="test-section">
            <h3>Eventi Form</h3>
            <button class="btn" onclick="testFormEvents()">Test Eventi</button>
            <div id="eventsResult" class="result-box"></div>
        </div>
        
        <div class="test-section">
            <h3>Console Errors</h3>
            <div id="consoleErrors" class="result-box">Monitoraggio errori attivo...</div>
        </div>
    </div>

    <!-- TAB TEST FORM -->
    <div id="form" class="tab-content">
        <h2>4. TEST FORM REGISTRAZIONE</h2>
        
        <div class="test-section">
            <h3>Test Completo Form</h3>
            <p>Questo test simula l'intero processo di registrazione:</p>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4>Dati Test</h4>
                    <input type="text" id="testEmail" value="test@example.com" placeholder="Email">
                    <input type="text" id="testName" value="Mario" placeholder="Nome">
                    <input type="text" id="testSurname" value="Rossi" placeholder="Cognome">
                    <select id="testCountry">
                        <option value="">Seleziona paese</option>
                        <option value="105">Italia</option>
                        <option value="226">USA</option>
                    </select>
                    <select id="testState">
                        <option value="">Prima seleziona paese</option>
                    </select>
                </div>
                <div>
                    <h4>Azioni Test</h4>
                    <button class="btn" onclick="testEmailCheck()">Test Verifica Email</button><br>
                    <button class="btn" onclick="testCountryChange()">Test Cambio Paese</button><br>
                    <button class="btn" onclick="testFormSubmit()">Test Invio Form</button><br>
                    <button class="btn" onclick="testValidation()">Test Validazione</button>
                </div>
            </div>
            
            <div id="formTestResult" class="result-box"></div>
        </div>
    </div>

    <!-- TAB SOLUZIONI -->
    <div id="solutions" class="tab-content">
        <h2>5. SOLUZIONI AI PROBLEMI</h2>
        
        <div id="solutionsList">
            <!-- Le soluzioni verranno generate dinamicamente -->
        </div>
        
        <div class="test-section">
            <h3>Fix Rapidi</h3>
            
            <div class="solution">
                <h4>1. Fix JavaScript baseURL</h4>
                <p>Se baseURL non è definito, aggiungi questo nell'head di create.account.tpl:</p>
                <div class="copy-code">&lt;script&gt;
var baseURL = '<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>';
console.log('baseURL impostato a:', baseURL);
&lt;/script&gt;</div>
            </div>
            
            <div class="solution">
                <h4>2. Fix Caricamento Stati</h4>
                <p>Se il caricamento degli stati non funziona, aggiungi questo script:</p>
                <div class="copy-code">$(document).ready(function() {
    $('#country').change(function() {
        var countryID = $(this).val();
        console.log('Paese selezionato:', countryID);
        
        if(countryID) {
            $.ajax({
                type: 'GET',
                url: baseURL + '/actions.php',
                data: {
                    "action": "stateList",
                    "countryID": countryID
                },
                dataType: 'json',
                success: function(data) {
                    console.log('Stati ricevuti:', data);
                    var options = '&lt;option value=""&gt;Seleziona stato&lt;/option&gt;';
                    $.each(data.states, function(id, name) {
                        options += '&lt;option value="' + id + '"&gt;' + name + '&lt;/option&gt;';
                    });
                    $('#state').html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Errore caricamento stati:', error);
                    alert('Errore nel caricamento degli stati');
                }
            });
        }
    });
});</div>
            </div>
            
            <div class="solution">
                <h4>3. Fix Validazione Form</h4>
                <p>Se la validazione non funziona, sostituisci la funzione submitAccountForm:</p>
                <div class="copy-code">function submitAccountForm() {
    $('.formErrorMessage').remove();
    $('*').removeClass('formError');
    
    var errors = [];
    
    // Verifica campi obbligatori
    if(!$('#f_name').val()) errors.push('Nome richiesto');
    if(!$('#l_name').val()) errors.push('Cognome richiesto');
    if(!$('#email').val()) errors.push('Email richiesta');
    if(!$('#password').val()) errors.push('Password richiesta');
    if($('#password').val().length < 6) errors.push('Password troppo corta');
    if($('#password').val() !== $('#vpassword').val()) errors.push('Password non coincidono');
    
    if(errors.length > 0) {
        alert('Errori trovati:\n' + errors.join('\n'));
        return false;
    }
    
    // Se tutto OK, invia form
    console.log('Form validato, invio in corso...');
    $('#createAccountForm')[0].submit();
    return true;
}</div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Inizializza baseURL se non definito
if(typeof baseURL === 'undefined') {
    var baseURL = '<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>';
}

// Array per tracciare errori
var jsErrors = [];
var originalConsoleError = console.error;

// Override console.error per catturare errori
console.error = function() {
    jsErrors.push(Array.from(arguments).join(' '));
    $('#consoleErrors').html('Errori JavaScript trovati: ' + jsErrors.length + '<br>' + jsErrors.join('<br>'));
    originalConsoleError.apply(console, arguments);
};

// Cattura errori JavaScript globali
window.onerror = function(msg, url, line, col, error) {
    var errorMsg = 'Errore: ' + msg + ' in ' + url + ' linea ' + line;
    jsErrors.push(errorMsg);
    $('#consoleErrors').html('Errori JavaScript trovati: ' + jsErrors.length + '<br>' + jsErrors.join('<br>'));
    return false;
};

// Funzioni di test
function showTab(tabName) {
    $('.tab').removeClass('active');
    $('.tab-content').removeClass('active');
    $('[onclick="showTab(\'' + tabName + '\')"]').addClass('active');
    $('#' + tabName).addClass('active');
}

function testStateList() {
    $('#stateResult').html('🔄 Testando endpoint stati...');
    
    $.ajax({
        type: 'GET',
        url: baseURL + '/actions.php',
        data: {"action": "stateList", "countryID": "105"},
        dataType: 'json',
        timeout: 10000,
        success: function(data) {
            var result = '<span class="success">✅ Endpoint stati funzionante</span><br>';
            result += 'Stati ricevuti: ' + Object.keys(data.states || {}).length + '<br>';
            result += '<details><summary>Dati completi</summary><pre>' + JSON.stringify(data, null, 2) + '</pre></details>';
            $('#stateResult').html(result);
        },
        error: function(xhr, status, error) {
            var result = '<span class="error">❌ Errore endpoint stati</span><br>';
            result += 'Status: ' + status + '<br>';
            result += 'Error: ' + error + '<br>';
            result += 'Response: ' + xhr.responseText + '<br>';
            $('#stateResult').html(result);
        }
    });
}

function testJavaScript() {
    var result = '';
    var score = 0;
    var total = 5;
    
    // Test jQuery
    if(typeof $ !== 'undefined') {
        result += '<span class="success">✅ jQuery caricato (v' + $.fn.jquery + ')</span><br>';
        score++;
    } else {
        result += '<span class="error">❌ jQuery NON caricato</span><br>';
    }
    
    // Test baseURL
    if(typeof baseURL !== 'undefined' && baseURL) {
        result += '<span class="success">✅ baseURL definito: ' + baseURL + '</span><br>';
        score++;
    } else {
        result += '<span class="error">❌ baseURL non definito o vuoto</span><br>';
    }
    
    // Test console
    if(typeof console !== 'undefined') {
        result += '<span class="success">✅ Console disponibile</span><br>';
        score++;
    } else {
        result += '<span class="error">❌ Console non disponibile</span><br>';
    }
    
    // Test JSON
    if(typeof JSON !== 'undefined') {
        result += '<span class="success">✅ JSON supportato</span><br>';
        score++;
    } else {
        result += '<span class="error">❌ JSON non supportato</span><br>';
    }
    
    // Test AJAX
    if(typeof $.ajax === 'function') {
        result += '<span class="success">✅ AJAX disponibile</span><br>';
        score++;
    } else {
        result += '<span class="error">❌ AJAX non disponibile</span><br>';
    }
    
    var percentage = Math.round((score / total) * 100);
    result += '<div class="progress"><div class="progress-bar" style="width: ' + percentage + '%">' + percentage + '% JavaScript OK</div></div>';
    
    $('#jsResult').html(result);
}

function testFunctions() {
    var result = '';
    var functions = [
        'getStateList',
        'displayFormError', 
        'checkRequired',
        'submitAccountForm',
        'checkEmail'
    ];
    
    var found = 0;
    functions.forEach(function(fname) {
        if(typeof window[fname] === 'function') {
            result += '<span class="success">✅ ' + fname + '</span><br>';
            found++;
        } else {
            result += '<span class="error">❌ ' + fname + ' non definita</span><br>';
        }
    });
    
    var percentage = Math.round((found / functions.length) * 100);
    result += '<div class="progress"><div class="progress-bar" style="width: ' + percentage + '%">' + percentage + '% Funzioni OK</div></div>';
    
    $('#functionsResult').html(result);
}

function testFormEvents() {
    var result = '';
    
    // Simula selezione paese
    $('#testCountry').off('change').on('change', function() {
        result += '✅ Evento change paese funziona<br>';
        $('#eventsResult').html(result);
    });
    
    result += '✅ Event listener impostato per test paese<br>';
    $('#eventsResult').html(result);
}

function testEmailCheck() {
    var email = $('#testEmail').val();
    $('#formTestResult').html('🔄 Testando verifica email: ' + email);
    
    $.ajax({
        type: 'POST',
        url: baseURL + '/actions.php',
        data: {
            "action": "checkEmail",
            "email": email
        },
        dataType: 'json',
        success: function(data) {
            var result = '<span class="success">✅ Verifica email funzionante</span><br>';
            result += 'Email esistenti trovate: ' + data.emailsReturned + '<br>';
            $('#formTestResult').html(result);
        },
        error: function(xhr, status, error) {
            $('#formTestResult').html('<span class="error">❌ Errore verifica email: ' + error + '</span>');
        }
    });
}

function testCountryChange() {
    var countryId = $('#testCountry').val();
    if(!countryId) {
        $('#formTestResult').html('<span class="warning">⚠️ Seleziona un paese per il test</span>');
        return;
    }
    
    $('#formTestResult').html('🔄 Testando cambio paese: ' + countryId);
    
    $.ajax({
        type: 'GET',
        url: baseURL + '/actions.php',
        data: {
            "action": "stateList",
            "countryID": countryId
        },
        dataType: 'json',
        success: function(data) {
            var states = data.states || {};
            var options = '<option value="">Seleziona stato</option>';
            
            $.each(states, function(id, name) {
                options += '<option value="' + id + '">' + name + '</option>';
            });
            
            $('#testState').html(options);
            
            var result = '<span class="success">✅ Cambio paese funzionante</span><br>';
            result += 'Stati caricati: ' + Object.keys(states).length + '<br>';
            $('#formTestResult').html(result);
        },
        error: function(xhr, status, error) {
            $('#formTestResult').html('<span class="error">❌ Errore cambio paese: ' + error + '</span>');
        }
    });
}

function testFormSubmit() {
    $('#formTestResult').html('🔄 Testando validazione form...');
    
    var errors = [];
    var email = $('#testEmail').val();
    var name = $('#testName').val();
    var surname = $('#testSurname').val();
    
    if(!email || !email.includes('@')) errors.push('Email non valida');
    if(!name) errors.push('Nome mancante');
    if(!surname) errors.push('Cognome mancante');
    
    var result = '';
    if(errors.length > 0) {
        result = '<span class="warning">⚠️ Errori validazione:</span><br>' + errors.join('<br>');
    } else {
        result = '<span class="success">✅ Validazione form OK</span><br>';
        result += 'Dati pronti per invio: ' + name + ' ' + surname + ' (' + email + ')';
    }
    
    $('#formTestResult').html(result);
}

function testValidation() {
    var result = '';
    
    // Test se esistono funzioni di validazione
    if(typeof checkRequired === 'function') {
        result += '<span class="success">✅ checkRequired disponibile</span><br>';
    } else {
        result += '<span class="error">❌ checkRequired mancante</span><br>';
    }
    
    if(typeof displayFormError === 'function') {
        result += '<span class="success">✅ displayFormError disponibile</span><br>';
    } else {
        result += '<span class="error">❌ displayFormError mancante</span><br>';
    }
    
    $('#formTestResult').html(result);
}

// Auto-test all'avvio
$(document).ready(function() {
    console.log('Debug sistema avviato');
    console.log('baseURL:', baseURL);
    
    // Mostra soluzioni per errori trovati
    <?php if(!empty($errors_found)): ?>
    var errors = <?php echo json_encode($errors_found); ?>;
    var solutionsHtml = '<div class="critical"><h3>❌ PROBLEMI CRITICI TROVATI</h3>';
    errors.forEach(function(error) {
        solutionsHtml += '<p>• ' + error + '</p>';
    });
    solutionsHtml += '</div>';
    $('#solutionsList').html(solutionsHtml);
    <?php endif; ?>
});
</script>

</body>
</html>