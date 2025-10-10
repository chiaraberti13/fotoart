<?php

if (!defined('ART_PUZZLE_AUTOLOADER_REGISTERED')) {
    spl_autoload_register(function ($class) {
        $prefix = 'ArtPuzzle\\';
        $baseDir = __DIR__ . '/classes/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });

    define('ART_PUZZLE_AUTOLOADER_REGISTERED', true);
}
