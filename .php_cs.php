<?php

return (new PhpCsFixer\Config)
    ->setUsingCache(false)
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const']
        ]
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude([])
            ->in(__DIR__ . '/src'));
