<?php
/**
 * Gestione CORS unificata per tutto il sito
 * Include questo file all'inizio di ogni file PHP chiamato via AJAX
 */

// Domini permessi
$allowed_origins = array(
    'https://fotoartpuzzle.it',
    'https://www.fotoartpuzzle.it', 
    'http://fotoartpuzzle.it',
    'http://www.fotoartpuzzle.it'
);

// Ottieni l'origin della richiesta
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Se non c'è HTTP_ORIGIN, prova con HTTP_REFERER
if (empty($origin) && isset($_SERVER['HTTP_REFERER'])) {
    $parsed = parse_url($_SERVER['HTTP_REFERER']);
    if (isset($parsed['scheme']) && isset($parsed['host'])) {
        $origin = $parsed['scheme'] . '://' . $parsed['host'];
    }
}

// Accetta qualsiasi sottodominio di fotoartpuzzle.it
if (!empty($origin) && strpos($origin, 'fotoartpuzzle.it') !== false) {
    header("Access-Control-Allow-Origin: " . $origin);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    header("Vary: Origin"); // Importante per la cache
}

// Gestisci richieste OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit(0);
}
?>