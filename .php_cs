<?php
return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_whitespace_in_blank_line' => true,
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'no_unneeded_control_parentheses' => true,
        'standardize_not_equals' => true,
        'single_quote' => true,
        'no_leading_import_slash' => true,
        'no_empty_comment' => true,
        'no_empty_statement' => true,
    ))
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()->in(__DIR__)
    )
;
