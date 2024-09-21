<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'single_quote' => true, // Enforce single quotes
        'no_unused_imports' => true, // Remove unused imports
        'array_syntax' => ['syntax' => 'short'], // Enforce short array syntax
    ])
    ->setFinder($finder);
