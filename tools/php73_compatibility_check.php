#!/usr/bin/env php
<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$selfRelativePath = str_replace($root . DIRECTORY_SEPARATOR, '', __FILE__);

$excludedDirectories = [
    $root . DIRECTORY_SEPARATOR . 'vendor',
    $root . DIRECTORY_SEPARATOR . 'logs',
    $root . DIRECTORY_SEPARATOR . 'upload',
];

$deprecatedPatterns = [
    'create_function' => '/\bcreate_function\s*\(/i',
    'each' => '/\beach\s*\(/i',
    'case_insensitive_define' => '/\bdefine\s*\(\s*([\'\"])\\w+\1\s*,\s*[^,]+,\s*(true|TRUE)\s*\)/',
];

$violations = [];

$directoryIterator = new RecursiveDirectoryIterator(
    $root,
    RecursiveDirectoryIterator::SKIP_DOTS
);
$iterator = new RecursiveIteratorIterator($directoryIterator);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    $filePath = $fileInfo->getPathname();
    $relativePath = str_replace($root . DIRECTORY_SEPARATOR, '', $filePath);

    if ($relativePath === $selfRelativePath) {
        continue;
    }

    $shouldSkip = false;
    foreach ($excludedDirectories as $excludedDir) {
        if (strpos($filePath, $excludedDir . DIRECTORY_SEPARATOR) === 0) {
            $shouldSkip = true;
            break;
        }
    }

    if ($shouldSkip) {
        continue;
    }

    if ($fileInfo->getExtension() !== 'php') {
        continue;
    }

    $contents = file_get_contents($filePath);
    if ($contents === false) {
        fwrite(STDERR, "Impossibile leggere il file: {$filePath}" . PHP_EOL);
        continue;
    }

    foreach ($deprecatedPatterns as $key => $pattern) {
        if (preg_match($pattern, $contents, $matches, PREG_OFFSET_CAPTURE)) {
            $line = substr_count(substr($contents, 0, $matches[0][1]), "\n") + 1;
            $violations[] = [
                'type' => $key,
                'file' => $relativePath,
                'line' => $line,
            ];
        }
    }
}

if (!empty($violations)) {
    fwrite(STDERR, "Sono stati rilevati costrutti deprecati incompatibili con PHP 7.3:" . PHP_EOL);
    foreach ($violations as $violation) {
        $type = $violation['type'];
        switch ($type) {
            case 'create_function':
                $message = 'Uso di create_function()';
                break;
            case 'each':
                $message = 'Uso di each()';
                break;
            case 'case_insensitive_define':
                $message = 'Costante definita con flag case-insensitive';
                break;
            default:
                $message = $type;
        }

        fwrite(
            STDERR,
            sprintf(' - %s (file: %s, linea: %d)%s', $message, $violation['file'], $violation['line'], PHP_EOL)
        );
    }
    exit(1);
}

echo "Nessun costrutto deprecato rilevato.\n";
exit(0);
