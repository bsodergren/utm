<?php
/**
 *
 *   UTM Common Class
 *
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use UTM\Rules;

Rules::setFileHeaderComment('This file is part of the UTM package. (c) Bjorn');

$config = new Config();

return $config
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules(Rules::getCsFixerRules())
    ->setFinder(
        Finder::create()
            ->in(__DIR__)
    );
