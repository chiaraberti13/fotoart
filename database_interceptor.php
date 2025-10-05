<?php
/**
 * INTERCETTATORE QUERY DATABASE AVANZATO
 * Compatibile con PHP 5.3+ e multiple estensioni database
 * 
 * ISTRUZIONI:
 * 1. Includere DOPO file_tracer.php
 * 2. Sostituire le chiamate mysql_query con query_traced()
 * 3. Per query esistenti, usare find/replace per interceptarle automaticamente
 */

class DatabaseInterceptor {
    
    private $fileTracer;
    private $connectionInfo = array();
    private $queryStats = array();
    
    public function __construct($fileTracer) {
        $this->fileTracer = $fileTracer;
        $this->setupInterception();
    }
    
    /**
     * Setup completo dell'intercettazione
     */
    private function setupInterception() {
        // Intercetta mysql_* functions (PHP 5.3)
        $this->interceptMysqlFunctions();
        
        // Intercetta mysqli se disponibile
        if (extension_loaded('mysqli')) {
            $this->interceptMysqli();
        }
        
        // Setup per PDO se disponibile  
        if (extension_loaded('pdo')) {
            $this->setupPDOInterception();
        }
    }
    
    /**
     * Intercetta le funzioni mysql legacy
     */
    private function interceptMysqlFunctions() {
        // Override mysql_query se non già fatto
        if (function_exists('mysql_query') && !function_exists('original_mysql_query_backup')) {
            // Rename della funzione originale non è possibile in PHP, uso eval
            $this->createMysqlWrapper();
        }
    }
    
    /**
     * Crea wrapper per mysql_query
     */
    private function createMysqlWrapper() {
        // Definisci la funzione wrapper globale
        global $GLOBALS;
        $GLOBALS['db_interceptor'] = $this;
        
        // Istruzioni per l'utente su come sostituire le chiamate
        $this->logInterceptionInfo();
    }
    
    /**
     * Intercetta mysqli
     */
    private function interceptMysqli() {
        // Per mysqli serve wrapping della classe o sostituzione manuale
        // Fornirò istruzioni specifiche
    }
    
    /**
     * Setup per PDO
     */
    private function setupPDOInterception() {
        // Per PDO serve estendere la classe
        $this->createPDOWrapper();
    }
    
    /**
     * Crea wrapper PDO personalizzato
     */
    private function createPDOWrapper() {
        if (!class_exists('TracedPDO')) {
            $pdoCode = '
class TracedPDO extends PDO {
    private $interceptor;
    
    public function __construct($dsn, $username = null, $password = null, $options = null) {
        parent::__construct($dsn, $username, $password, $options);
        global $db_interceptor;
        $this->interceptor = $db_interceptor;
    }
    
    public function query($statement) {
        if ($this->interceptor) {
            $this->interceptor->traceQuery($statement, "PDO::query");
        }
        return parent::query($statement);
    }
    
    public function exec($statement) {
        if ($this->interceptor) {
            $this->interceptor->traceQuery($statement, "PDO::exec");
        }
        return parent::exec($statement);
    }
    
    public function prepare($statement, $driver_options = array()) {
        $stmt = parent::prepare($statement, $driver_options);
        return new TracedPDOStatement($stmt, $statement, $this->interceptor);
    }
}

class TracedPDOStatement {
    private $stmt;
    private $query;
    private $interceptor;
    
    public function __construct($stmt, $query, $interceptor) {
        $this->stmt = $stmt;
        $this->query = $query;
        $this->interceptor = $interceptor;
    }
    
    public function execute($input_parameters = null) {
        if ($this->interceptor) {
            $this->interceptor->traceQuery($this->query, "PDOStatement::execute", $input_parameters);
        }
        return $this->stmt->execute($input_parameters);
    }
    
    // Delega tutti gli altri metodi al statement originale
    public function __call($method, $args) {
        return call_user_func_array(array($this->stmt, $method), $args);
    }
}';
            eval($pdoCode);
        }
    }
    
    /**
     * Funzione principale per tracciare le query
     */
    public function traceQuery($query, $method = 'unknown', $parameters = null) {
        // Risolvi i parametri se è una prepared statement
        $resolvedQuery = $this->resolveParameters($query, $parameters);
        
        // Log tramite FileTracer
        if ($this->fileTracer) {
            $this->fileTracer->logDatabaseQuery($resolvedQuery, $method);
        }
        
        // Statistiche aggiuntive
        $this->updateQueryStats($resolvedQuery, $method);
        
        return $resolvedQuery;
    }
    
    /**
     * Risolve i parametri nelle prepared statement
     */
    private function resolveParameters($query, $parameters) {
        if (!$parameters || !is_array($parameters)) {
            return $query;
        }
        
        // Sostituisce i placeholder con i valori reali
        foreach ($parameters as $key => $value) {
            $placeholder = is_numeric($key) ? '?' : ':' . $key;
            $safeValue = is_string($value) ? "'" . addslashes($value) . "'" : $value;
            $query = str_replace($placeholder, $safeValue, $query);
        }
        
        return $query;
    }
    
    /**
     * Aggiorna statistiche query
     */
    private function updateQueryStats($query, $method) {
        $type = $this->getQueryType($query);
        
        if (!isset($this->queryStats[$type])) {
            $this->queryStats[$type] = 0;
        }
        $this->queryStats[$type]++;
    }
    
    /**
     * Ottiene il tipo di query
     */
    private function getQueryType($query) {
        $query = trim(strtoupper($query));
        if (strpos($query, 'SELECT') === 0) return 'SELECT';
        if (strpos($query, 'INSERT') === 0) return 'INSERT';
        if (strpos($query, 'UPDATE') === 0) return 'UPDATE';
        if (strpos($query, 'DELETE') === 0) return 'DELETE';
        return 'OTHER';
    }
    
    /**
     * Log delle informazioni di intercettazione
     */
    private function logInterceptionInfo() {
        error_log("Database Interceptor attivato - Per intercettare completamente le query:");
        error_log("1. Sostituire mysql_query() con query_traced()");
        error_log("2. Sostituire new PDO() con new TracedPDO()");
        error_log("3. Sostituire mysqli_query() con mysqli_query_traced()");
    }
    
    /**
     * Ottiene le statistiche query
     */
    public function getQueryStats() {
        return $this->queryStats;
    }
}

// Funzioni wrapper globali per intercettazione

/**
 * Wrapper per mysql_query
 */
function query_traced($query, $connection = null) {
    global $db_interceptor;
    if ($db_interceptor) {
        $db_interceptor->traceQuery($query, 'mysql_query');
    }
    
    if ($connection) {
        return mysql_query($query, $connection);
    }
    return mysql_query($query);
}

/**
 * Wrapper per mysqli_query
 */
function mysqli_query_traced($connection, $query) {
    global $db_interceptor;
    if ($db_interceptor) {
        $db_interceptor->traceQuery($query, 'mysqli_query');
    }
    
    return mysqli_query($connection, $query);
}

/**
 * Wrapper per query generiche
 */
function execute_traced_query($query, $connection = null, $method = 'generic') {
    global $db_interceptor;
    if ($db_interceptor) {
        $db_interceptor->traceQuery($query, $method);
    }
    
    // Determina automaticamente il metodo
    if (is_resource($connection) && get_resource_type($connection) === 'mysql link') {
        return mysql_query($query, $connection);
    } elseif ($connection instanceof mysqli) {
        return $connection->query($query);
    } elseif ($connection instanceof PDO) {
        return $connection->query($query);
    }
    
    // Fallback a mysql_query senza connessione
    return mysql_query($query);
}

// Inizializza l'interceptor se il FileTracer è disponibile
if (isset($GLOBALS['fileTracer'])) {
    $GLOBALS['db_interceptor'] = new DatabaseInterceptor($GLOBALS['fileTracer']);
    echo "<!-- Database Interceptor attivato -->\n";
}

?>