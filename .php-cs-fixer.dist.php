<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,

        'declare_strict_types' => true,

        'ordered_imports' => true,

        'no_unused_imports' => true,

        'single_quote' => true,

        'trailing_comma_in_multiline' => true,

        'array_syntax' => [
            'syntax' => 'short',
        ],
    ])
    ->setFinder($finder);
