<?php
/**
 *
 *   Plexweb
 *
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$fileHeaderComment = <<<'EOF'
Command like Metatag writer for video files.
EOF;


/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.59.3|configurator
 * you can change this configuration by importing this file.
 */
$config            = new PhpCsFixer\Config();
return $config
->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0'                               => true,
        '@PER-CS2.0:risky'                         => true,
        'binary_operator_spaces'                   => ['default' => 'align_by_scope'],
        'header_comment'                           => ['header' => $fileHeaderComment, 'comment_type' => 'PHPDoc', 'location' => 'after_open', 'separate' => 'bottom'],

        'assign_null_coalescing_to_coalesce_equal' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
        ->in(__DIR__),
        // ->exclude([
        //     'folder-to-exclude',
        // ])
        // ->append([
        //     'file-to-include',
        // ])
    )
;
