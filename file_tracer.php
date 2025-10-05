<?php
/**
 * TRACCIATORE COMPLETO DI DIPENDENZE PHP 5.3
 * Compatibile con PHP 5.3+ 
 * 
 * ISTRUZIONI:
 * 1. Inserire questo file nella root del progetto
 * 2. Includere all'inizio di index.php: require_once 'file_tracer.php';
 * 3. Il report verrà generato in /reports/dependency_report.html
 */

class FileTracer {
    
    private $startTime;
    private $includedFiles = array();
    private $includeOrder = array();
    private $includeStack = array();
    private $queryLog = array();
    private $tableUsage = array();
    private $executionPath = array();
    private $originalMysqlQuery;
    
    public function __construct() {
        $this->startTime = microtime(true);
        $this->setupFileTracking();
        $this->setupDatabaseTracking();
        $this->setupShutdownHandler();
        
        // Log del file iniziale
        $this->logFileInclude($_SERVER['SCRIPT_FILENAME'], 'ENTRY_POINT', 0);
    }
    
    /**
     * Setup del tracciamento file tramite autoloader e include path
     */
    private function setupFileTracking() {
        // Registra un autoloader personalizzato per tracciare i file
        spl_autoload_register(array($this, 'traceAutoload'));
        
        // Override delle funzioni include/require (tramite output buffering trick)
        $this->setupIncludeTracking();
    }
    
    /**
     * Traccia gli autoload delle classi
     */
    public function traceAutoload($className) {
        $backtrace = debug_backtrace();
        $caller = isset($backtrace[1]) ? $backtrace[1] : array('file' => 'unknown', 'line' => 0);
        
        $this->logFileInclude($className . '.class.php', 'AUTOLOAD', $caller['line'], $caller['file']);
        return false; // Non interferire con altri autoloader
    }
    
    /**
     * Setup tracking degli include/require
     */
    private function setupIncludeTracking() {
        // Salva i file già inclusi all'avvio
        $initialFiles = get_included_files();
        foreach ($initialFiles as $index => $file) {
            $this->logFileInclude($file, 'INITIAL', $index);
        }
    }
    
    /**
     * Setup del tracciamento database
     */
    private function setupDatabaseTracking() {
        // Backup della funzione originale mysql_query
        if (function_exists('mysql_query')) {
            $this->originalMysqlQuery = 'mysql_query';
            $this->interceptMysqlQueries();
        }
        
        // Intercetta anche le query PDO se utilizzate
        $this->interceptPDOQueries();
    }
    
    /**
     * Intercetta le query MySQL legacy
     */
    private function interceptMysqlQueries() {
        // Crea wrapper per mysql_query
        if (!function_exists('original_mysql_query')) {
            $code = '
            function original_mysql_query($query, $connection = null) {
                global $fileTracer;
                if ($fileTracer) {
                    $fileTracer->logDatabaseQuery($query, "mysql_query");
                }
                return $connection ? mysql_query($query, $connection) : mysql_query($query);
            }';
            eval($code);
        }
    }
    
    /**
     * Intercetta le query PDO
     */
    private function interceptPDOQueries() {
        // Nota: Per PDO serve un approccio diverso tramite estensione della classe
        // Questo sarà implementato se il progetto usa PDO
    }
    
    /**
     * Log di un file incluso
     */
    public function logFileInclude($filename, $type = 'INCLUDE', $line = 0, $callerFile = '') {
        $realPath = realpath($filename) ?: $filename;
        $relativePath = $this->getRelativePath($realPath);
        
        $includeInfo = array(
            'timestamp' => microtime(true) - $this->startTime,
            'order' => count($this->includeOrder) + 1,
            'filename' => basename($filename),
            'full_path' => $realPath,
            'relative_path' => $relativePath,
            'type' => $type,
            'caller_file' => $callerFile ? basename($callerFile) : '',
            'caller_line' => $line,
            'file_size' => file_exists($realPath) ? filesize($realPath) : 0,
            'backtrace' => $this->getSimplifiedBacktrace()
        );
        
        $this->includeOrder[] = $includeInfo;
        $this->includedFiles[$realPath] = $includeInfo;
    }
    
    /**
     * Log di una query database
     */
    public function logDatabaseQuery($query, $method = 'unknown') {
        $backtrace = debug_backtrace();
        $caller = $this->findRelevantCaller($backtrace);
        
        // Analizza la query per estrarre le tabelle
        $tables = $this->extractTablesFromQuery($query);
        $queryType = $this->getQueryType($query);
        $category = $this->categorizeQuery($query, $caller['file']);
        
        $queryInfo = array(
            'timestamp' => microtime(true) - $this->startTime,
            'query' => $query,
            'method' => $method,
            'type' => $queryType,
            'category' => $category,
            'tables' => $tables,
            'caller_file' => $caller['file'],
            'caller_line' => $caller['line'],
            'caller_function' => $caller['function']
        );
        
        $this->queryLog[] = $queryInfo;
        
        // Aggiorna statistiche tabelle
        foreach ($tables as $table) {
            if (!isset($this->tableUsage[$table])) {
                $this->tableUsage[$table] = array(
                    'select' => 0, 'insert' => 0, 'update' => 0, 'delete' => 0,
                    'categories' => array(), 'files' => array()
                );
            }
            $this->tableUsage[$table][strtolower($queryType)]++;
            $this->tableUsage[$table]['categories'][$category] = true;
            $this->tableUsage[$table]['files'][$caller['file']] = true;
        }
    }
    
    /**
     * Estrae le tabelle da una query SQL
     */
    private function extractTablesFromQuery($query) {
        $tables = array();
        $query = preg_replace('/\s+/', ' ', trim($query));
        
        // Pattern per diverse tipologie di query
        $patterns = array(
            '/FROM\s+([a-zA-Z_][a-zA-Z0-9_]*)/i',
            '/INTO\s+([a-zA-Z_][a-zA-Z0-9_]*)/i',
            '/UPDATE\s+([a-zA-Z_][a-zA-Z0-9_]*)/i',
            '/JOIN\s+([a-zA-Z_][a-zA-Z0-9_]*)/i',
        );
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $query, $matches)) {
                $tables = array_merge($tables, $matches[1]);
            }
        }
        
        return array_unique($tables);
    }
    
    /**
     * Determina il tipo di query
     */
    private function getQueryType($query) {
        $query = trim(strtoupper($query));
        if (strpos($query, 'SELECT') === 0) return 'SELECT';
        if (strpos($query, 'INSERT') === 0) return 'INSERT';
        if (strpos($query, 'UPDATE') === 0) return 'UPDATE';
        if (strpos($query, 'DELETE') === 0) return 'DELETE';
        if (strpos($query, 'CREATE') === 0) return 'CREATE';
        if (strpos($query, 'DROP') === 0) return 'DROP';
        if (strpos($query, 'ALTER') === 0) return 'ALTER';
        return 'OTHER';
    }
    
    /**
     * Categorizza la query in base al contesto
     */
    private function categorizeQuery($query, $callerFile) {
        $filename = strtolower(basename($callerFile));
        $queryLower = strtolower($query);
        
        // Categorie basate sul nome del file
        if (strpos($filename, 'login') !== false || strpos($filename, 'auth') !== false) {
            return 'AUTHENTICATION';
        }
        if (strpos($filename, 'register') !== false || strpos($filename, 'signup') !== false) {
            return 'REGISTRATION';
        }
        if (strpos($filename, 'order') !== false || strpos($filename, 'cart') !== false) {
            return 'ORDERS';
        }
        if (strpos($filename, 'product') !== false || strpos($filename, 'catalog') !== false) {
            return 'PRODUCTS';
        }
        if (strpos($filename, 'user') !== false || strpos($filename, 'profile') !== false) {
            return 'USER_MANAGEMENT';
        }
        if (strpos($filename, 'admin') !== false) {
            return 'ADMINISTRATION';
        }
        
        // Categorie basate sul contenuto della query
        if (strpos($queryLower, 'password') !== false || strpos($queryLower, 'session') !== false) {
            return 'AUTHENTICATION';
        }
        if (strpos($queryLower, 'order') !== false || strpos($queryLower, 'payment') !== false) {
            return 'ORDERS';
        }
        
        return 'GENERAL';
    }
    
    /**
     * Trova il caller rilevante nel backtrace
     */
    private function findRelevantCaller($backtrace) {
        foreach ($backtrace as $trace) {
            if (isset($trace['file']) && strpos($trace['file'], __FILE__) === false) {
                return array(
                    'file' => basename($trace['file']),
                    'line' => isset($trace['line']) ? $trace['line'] : 0,
                    'function' => isset($trace['function']) ? $trace['function'] : 'unknown'
                );
            }
        }
        return array('file' => 'unknown', 'line' => 0, 'function' => 'unknown');
    }
    
    /**
     * Ottiene un backtrace semplificato
     */
    private function getSimplifiedBacktrace() {
        $backtrace = debug_backtrace();
        $simplified = array();
        
        foreach ($backtrace as $trace) {
            if (isset($trace['file']) && strpos($trace['file'], __FILE__) === false) {
                $simplified[] = basename($trace['file']) . ':' . (isset($trace['line']) ? $trace['line'] : '?');
            }
        }
        
        return array_slice($simplified, 0, 3); // Primi 3 livelli
    }
    
    /**
     * Calcola il percorso relativo
     */
    private function getRelativePath($fullPath) {
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        if (strpos($fullPath, $documentRoot) === 0) {
            return substr($fullPath, strlen($documentRoot));
        }
        return $fullPath;
    }
    
    /**
     * Setup dell'handler di shutdown
     */
    private function setupShutdownHandler() {
        register_shutdown_function(array($this, 'generateReport'));
    }
    
    /**
     * Aggiorna la lista dei file inclusi
     */
    public function updateIncludedFiles() {
        $currentFiles = get_included_files();
        foreach ($currentFiles as $file) {
            $realPath = realpath($file);
            if (!isset($this->includedFiles[$realPath])) {
                $this->logFileInclude($file, 'RUNTIME', 0);
            }
        }
    }
    
    /**
     * Genera il report finale
     */
    public function generateReport() {
        $this->updateIncludedFiles();
        
        $reportDir = dirname($_SERVER['SCRIPT_FILENAME']) . '/reports';
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0755, true);
        }
        
        $reportFile = $reportDir . '/dependency_report_' . date('Y-m-d_H-i-s') . '.html';
        $htmlReport = $this->generateHTMLReport();
        
        file_put_contents($reportFile, $htmlReport);
        
        // Genera anche un report JSON per parsing automatico
        $jsonReport = $reportDir . '/dependency_report_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($jsonReport, json_encode(array(
            'files' => $this->includeOrder,
            'queries' => $this->queryLog,
            'table_usage' => $this->tableUsage,
            'summary' => $this->generateSummary()
        ), JSON_PRETTY_PRINT));
    }
    
    /**
     * Genera il report HTML
     */
    private function generateHTMLReport() {
        $executionTime = microtime(true) - $this->startTime;
        $summary = $this->generateSummary();
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Report Dipendenze PHP - <?php echo date('Y-m-d H:i:s'); ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1, h2, h3 { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px; }
                .summary { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .section { margin: 30px 0; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { padding: 8px 12px; border: 1px solid #ddd; text-align: left; }
                th { background: #007cba; color: white; }
                tr:nth-child(even) { background: #f9f9f9; }
                .file-type { padding: 3px 8px; border-radius: 3px; font-size: 0.8em; color: white; }
                .type-ENTRY { background: #28a745; }
                .type-INCLUDE { background: #007cba; }
                .type-AUTOLOAD { background: #ffc107; color: #000; }
                .type-RUNTIME { background: #6c757d; }
                .query-type { padding: 3px 8px; border-radius: 3px; font-size: 0.8em; color: white; }
                .query-SELECT { background: #17a2b8; }
                .query-INSERT { background: #28a745; }
                .query-UPDATE { background: #ffc107; color: #000; }
                .query-DELETE { background: #dc3545; }
                .category { padding: 3px 8px; border-radius: 3px; font-size: 0.8em; background: #6f42c1; color: white; }
                .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; font-size: 0.9em; overflow-x: auto; }
                .stats { display: flex; gap: 20px; flex-wrap: wrap; }
                .stat-box { background: #007cba; color: white; padding: 15px; border-radius: 5px; text-align: center; min-width: 120px; }
                .stat-number { font-size: 1.5em; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>📊 Report Completo Dipendenze PHP</h1>
                <p><strong>Generato:</strong> <?php echo date('Y-m-d H:i:s'); ?> | <strong>Tempo esecuzione:</strong> <?php echo number_format($executionTime, 3); ?>s</p>
                
                <div class="summary">
                    <h2>📈 Riepilogo Esecuzione</h2>
                    <div class="stats">
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $summary['total_files']; ?></div>
                            <div>File Totali</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $summary['total_queries']; ?></div>
                            <div>Query Database</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $summary['total_tables']; ?></div>
                            <div>Tabelle Usate</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo number_format($summary['total_size'] / 1024, 1); ?>KB</div>
                            <div>Dimensione Totale</div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2>📁 File Inclusi (Ordine di Caricamento)</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>Tipo</th>
                                <th>Nome File</th>
                                <th>Percorso</th>
                                <th>Dimensione</th>
                                <th>Chiamato da</th>
                                <th>Linea</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->includeOrder as $file): ?>
                            <tr>
                                <td><?php echo $file['order']; ?></td>
                                <td><?php echo number_format($file['timestamp'], 3); ?>s</td>
                                <td><span class="file-type type-<?php echo $file['type']; ?>"><?php echo $file['type']; ?></span></td>
                                <td><strong><?php echo htmlspecialchars($file['filename']); ?></strong></td>
                                <td><code><?php echo htmlspecialchars($file['relative_path']); ?></code></td>
                                <td><?php echo number_format($file['file_size']); ?> bytes</td>
                                <td><?php echo htmlspecialchars($file['caller_file']); ?></td>
                                <td><?php echo $file['caller_line']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($this->queryLog)): ?>
                <div class="section">
                    <h2>🗄️ Log Query Database</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Tipo</th>
                                <th>Categoria</th>
                                <th>Tabelle</th>
                                <th>Query</th>
                                <th>File</th>
                                <th>Linea</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->queryLog as $query): ?>
                            <tr>
                                <td><?php echo number_format($query['timestamp'], 3); ?>s</td>
                                <td><span class="query-type query-<?php echo $query['type']; ?>"><?php echo $query['type']; ?></span></td>
                                <td><span class="category"><?php echo $query['category']; ?></span></td>
                                <td><?php echo implode(', ', $query['tables']); ?></td>
                                <td><div class="code"><?php echo htmlspecialchars(substr($query['query'], 0, 100)) . (strlen($query['query']) > 100 ? '...' : ''); ?></div></td>
                                <td><?php echo htmlspecialchars($query['caller_file']); ?></td>
                                <td><?php echo $query['caller_line']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h2>📊 Utilizzo Tabelle Database</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Tabella</th>
                                <th>SELECT</th>
                                <th>INSERT</th>
                                <th>UPDATE</th>
                                <th>DELETE</th>
                                <th>Categorie</th>
                                <th>File Coinvolti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->tableUsage as $table => $usage): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($table); ?></strong></td>
                                <td><?php echo $usage['select']; ?></td>
                                <td><?php echo $usage['insert']; ?></td>
                                <td><?php echo $usage['update']; ?></td>
                                <td><?php echo $usage['delete']; ?></td>
                                <td><?php echo implode(', ', array_keys($usage['categories'])); ?></td>
                                <td><?php echo implode(', ', array_keys($usage['files'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Genera statistiche di riepilogo
     */
    private function generateSummary() {
        $totalSize = 0;
        foreach ($this->includedFiles as $file) {
            $totalSize += $file['file_size'];
        }
        
        return array(
            'total_files' => count($this->includedFiles),
            'total_queries' => count($this->queryLog),
            'total_tables' => count($this->tableUsage),
            'total_size' => $totalSize,
            'execution_time' => microtime(true) - $this->startTime
        );
    }
}

// Inizializza il tracciatore globale
$GLOBALS['fileTracer'] = new FileTracer();

// Hook per le query mysql_query (se usata)
if (!function_exists('mysql_query_traced')) {
    function mysql_query_traced($query, $connection = null) {
        global $fileTracer;
        if ($fileTracer) {
            $fileTracer->logDatabaseQuery($query, 'mysql_query');
        }
        return $connection ? mysql_query($query, $connection) : mysql_query($query);
    }
}

echo "<!-- Tracciatore PHP attivato - Report generato alla fine dell'esecuzione -->\n";
?>