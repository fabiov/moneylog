<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('data')
    ->exclude('docker')
    ->exclude('vendor')
//    ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'array_indentation' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_unused_imports' => true,
    'strict_param' => true,
    'visibility_required' => ['property', 'method', 'const'],
    // 'compact_nullable_typehint' => true,
    // 'dir_constant' => true,
    // 'ereg_to_preg' => true,
    // 'fopen_flag_order' => true,
    // 'fopen_flags' => true,
    // 'heredoc_indentation' => true,
    // 'heredoc_to_nowdoc' => true,
    // 'implode_call' => true,
    // 'include' => true,
    // 'is_null' => true,
    // 'modernize_types_casting' => true,
    // 'no_alias_functions' => true,
    // 'no_unset_on_property' => true,
    // 'no_useless_else' => true,
    // 'no_useless_return' => true,
    // 'self_accessor' => false,
    // 'ternary_to_null_coalescing' => true,
    // 'void_return' => false,
])
    ->setFinder($finder);
