<?php

declare(strict_types=1);

return [
    'target_php_version'              => '7.1',
    'directory_list'                  => ['src/', 'vendor/'],
    'exclude_analysis_directory_list' => [
        'vendor/',
        'src/Opulence/Framework/Console/Testing',
        'src/Opulence/Framework/Http/Testing',
        'src/Opulence/Routing/Tests'
    ],
    'quick_mode' => true,
    'analyze_signature_compatibility' => true,
    'minimum_severity' => 0,
    'allow_missing_properties' => false,
    'null_casts_as_any_type' => false,
    'null_casts_as_array' => false,
    'array_casts_as_null' => false,
    'scalar_implicit_cast' => true, // TODO: Consider removing
    'scalar_implicit_partial' => [],
    'ignore_undeclared_variables_in_global_scope' => true, // TODO: No globals!
    'suppress_issue_types' => [
        'PhanUnreferencedUseNormal',
    ],
    'exclude_file_regex' => '@.*Test(Case)?.php$@',
];
