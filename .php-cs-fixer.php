<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.59.3|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PER-CS:risky' => true,
        // Multi-line arrays, arguments list, parameters list and `match` expressions must have a trailing comma.
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude([
                'src/data/',
            ])
            ->append([
                'bin/build',
                __FILE__,
            ])
    )
;
