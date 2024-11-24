<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/config')
    ->in(__DIR__ . '/routes')
    ->in(__DIR__ . '/public')
    ->in(__DIR__ . '/database');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        // Другие правила по необходимости
    ])
    ->setFinder($finder);