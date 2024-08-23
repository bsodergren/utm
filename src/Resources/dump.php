<?php
/**
 * Command like Metatag writer for video files.
 */

use Symfony\Component\VarDumper\Caster\ScalarStub;
use Symfony\Component\VarDumper\VarDumper;
use UTM\Utilities\Debug\Debug;
use UTM\Utm;

function DumpServerExists()
{
    $fp = @fsockopen('tcp://127.0.0.1', 9912, $errno, $errstr, 1);
    if ($fp) {
        return true;
    }

    return Utm::$SHOW_HTML_DUMP;
}

if (!function_exists('UtmDump')) {
    /**
     * @author Nicolas Grekas <p@tchwork.com>
     * @author Alexandre Daubois <alex.daubois@gmail.com>
     */
    function UtmDump(mixed ...$vars): mixed
    {
        if (false == DumpServerExists()) {
            return null;
        }

        if (!$vars) {
            VarDumper::dump(new ScalarStub('🐛'));

            return null;
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            VarDumper::dump($vars[0]);
            $k = 0;
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        if (1 < count($vars)) {
            return $vars;
        }

        return $vars[$k];
    }
}

if (!function_exists('Utmdd')) {
    function Utmdd(mixed ...$vars): mixed
    {
        if (false == DumpServerExists()) {
            return null;
        }

        if (!in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            VarDumper::dump($vars[0]);
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        exit(1);
    }
}

if (!function_exists('utminfo')) {
    function utminfo(mixed ...$vars)
    {
        Debug::info(TimerNow(), $vars);
    }
}
if (!function_exists('utmddump')) {
    function utmddump()
    {
        Debug::ddump();
    }
}

if (!function_exists('utmshutdown')) {
    function utmshutdown($file = null)
    {
        Debug::info(TimerNow());
        if (null === $file) {
            Debug::ddump();
        } else {
            Debug::writedump($file);
        }
    }
}
