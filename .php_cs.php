<?php

return (new PhpCsFixer\Config)
    ->setUsingCache(false)
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude([])
            ->in(__DIR__ . '/src'));
