<?php

$rules = [
    '@Symfony' => true,
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    'array_syntax' => ['syntax' => 'short'],
    'indentation_type' => true,
    'single_quote' => true,
    'class_attributes_separation' => ['elements' => ['method' => 'one']],
    'no_unused_imports' => true,
    'binary_operator_spaces' => true,
    'blank_line_before_statement' => [
        'statements' => ['return', 'if', 'for', 'foreach', 'while', 'switch', 'break', 'continue', 'declare', 'try'],
    ],
    'strict_comparison' => true,
    'line_ending' => true,

    // Правила для док-блоков
    'align_multiline_comment' => true, // Выравнивает многострочные комментарии
    'general_phpdoc_tag_rename' => true, // Исправляет теги (например, @inheritDoc -> @inheritdoc)
];

$finder = new PhpCsFixer\Finder()
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->exclude('var');

return new PhpCsFixer\Config()->setRules(rules: $rules)->setFinder(finder: $finder);
