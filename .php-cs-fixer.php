<?php

declare(strict_types=1);

use Kaiseki\CodingStandard\PhpCsFixerConfig;
use PhpCsFixer\Finder;

$finder = new Finder();

$finder
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->files()
    ->name('*.php');

return PhpCsFixerConfig::get($finder);
