<?php
/**
 * UTM Common classes
 */

use UTM\Utilities\Option;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!function_exists('OptionIsTrue')) {
    function OptionIsTrue($var)
    {
        return Option::isTrue($var);
    }
}

if (!function_exists('OptionIsFalse')) {
    function OptionIsFalse($var)
    {
        return Option::isFalse($var);
    }
}
