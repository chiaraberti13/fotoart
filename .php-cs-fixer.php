<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/modules/fotoartpuzzle'])
    ->name('*.php')
    ->ignoreDotFiles(false);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'single_quote' => true,
    ])
    ->setFinder($finder);
