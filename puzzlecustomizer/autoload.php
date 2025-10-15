<?php

spl_autoload_register(function ($className) {
    $classMap = [
        'PuzzleCustomization' => 'PuzzleCustomization.php',
        'PuzzleBoxColor' => 'PuzzleBoxColor.php',
        'PuzzleCartManager' => 'PuzzleCartManager.php',
        'PuzzleFont' => 'PuzzleFont.php',
        'PuzzleImageFormat' => 'PuzzleImageFormat.php',
        'PuzzleOption' => 'PuzzleOption.php',
        'PuzzleProduct' => 'PuzzleProduct.php',
        'PuzzleTextColor' => 'PuzzleTextColor.php',
        'ImageProcessor' => 'ImageProcessor.php',
        'PuzzleImageProcessorException' => 'ImageProcessor.php',
    ];

    if (isset($classMap[$className])) {
        $file = __DIR__ . '/classes/' . $classMap[$className];
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
