<?php

require_once __DIR__ . '/TokenServiceTest.php';
require_once __DIR__ . '/PathValidatorTest.php';
require_once __DIR__ . '/SessionServiceTest.php';

$tests = [
    new TokenServiceTest(),
    new PathValidatorTest(),
    new SessionServiceTest(),
];

$totalAssertions = 0;

foreach ($tests as $test) {
    $test->run();
    $totalAssertions += $test->getAssertionCount();
}

echo 'All fotoartpuzzle tests passed (' . $totalAssertions . " assertions)\n";
