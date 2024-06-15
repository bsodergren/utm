<?php
/**
 * Command like Metatag writer for video files.
 */

namespace UTM\Utilities\Debug;

use UTM\Bundle\Monolog\UTMLog;

class Debug
{
    public $traceStripPrefix = 'ore';
    public static $DebugArray = [];

    private static $padding = [
        'file' => 0,
        'class' => 0,
        'function' => 0,
        'line' => 0,
    ];

    private static $color = [
        'file' => ['red'],
        'class' => ['yellow'],
        'function' => ['blue'],
        'line' => ['green'],
    ];

    public static function info($var)
    {
        // $calling_func = self::tracePath();
        $class = '';
        $trace = debug_backtrace();
        $file = $trace[1]['file'];
        $line = $trace[1]['line'];
        $func = '';
        if (array_key_exists('2', $trace)) {
            $file = $trace[2]['file'];
            $line = $trace[2]['line'];
            if (array_key_exists('class', $trace)) {
                $class = $trace[2]['class'].'::';
            }
            $func = '->'.$class.$trace[2]['function'];
        }
        $root = dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1);

        $calling_func = str_replace($root, '', $file).':'.$line.$func;
        self::$DebugArray[] = ['page' => $calling_func, 'Data' => $var];
    }

    public static function ddump()
    {
        utmdump(self::$DebugArray);
    }

    public static function print_array($array, $die = 0)
    {
        print_r($array);
        if (1 == $die) {
            exit(\PHP_EOL);
        }
    }

    public static function tracePath()
    {
        $root = dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1);

        $trace = debug_backtrace();

        foreach ($trace as $i => $row) {
            $arg = [];

            if (str_contains($row['file'], 'vendor')) {
                continue;
            }

            if (\array_key_exists('class', $row)) {
                if (str_contains($row['class'], 'Symfony')) {
                    continue;
                }
                if (str_contains($row['class'], 'Debug')) {
                    continue;
                }
                if (str_contains($row['class'], 'Monolog')) {
                    continue;
                }
                if (str_contains($row['class'], 'Monolog')) {
                    if (str_contains($row['function'], 'logger')) {
                        $calledFile = self::returnTrace('file', $row);
                        $calledLine = self::returnTrace('line', $row);
                    }
                    continue;
                }
            }
            // if (str_contains($row['function'], 'require')) {
            //     continue;
            // }
            // if (str_contains($row['function'], 'utminfo')) {
            //     continue;
            // }
            if (\array_key_exists('args', $row)) {
                $args = $row['args'];
                if (\is_array($args)) {
                    foreach ($args as $k => $value) {
                        if ('' != $value) {
                            if (\is_array($value)) {
                                continue;
                            }
                            if (\is_object($value)) {
                                continue;
                            }
                            $arg[] = "'".$value."'";
                        }
                    }
                }
            }
            $arguments = implode(',', $arg);
            $arguments = str_replace($root, '', $arguments);
            $classArray[] = self::getClassPath($row['class'], 2).':'.$row['function'].'('.$arguments.')';
        }

        $classArray = array_reverse($classArray);
        foreach ($classArray as $k => $classPath) {
            [$class,$method] = explode(':', $classPath);
            $class = str_replace('\\', '_', $class);
            $path[$class][] = $method;
        }

        foreach ($path as $classPath => $methods) {
            $classPath = str_replace('_', '\\', $classPath);
            if (\is_array($methods)) {
                $level = 4;
                $spaces = str_repeat('', $level * 4);
                $methodPath = implode($spaces.'->', $methods);
            }
            $fullPath[] = $classPath.':'.$methodPath;
        }
        $level = 1;
        $spaces = str_repeat(' ', $level * 4);

        $fullPath = str_replace($root, '', $fullPath);

        return implode($spaces.'->', $fullPath);
    }

    public static function CallingFunctionName()
    {
        $trace = debug_backtrace();
        $TraceList = '';

        $class = str_pad('', self::$padding['class'], ' ');
        $calledFile = str_pad('', self::$padding['file'], ' ');
        $calledLine = str_pad('', self::$padding['line'], ' ');
        $function = str_pad('', self::$padding['function'], ' ');

        foreach ($trace as $key => $row) {
            if (\array_key_exists('class', $row)) {
                if (str_contains($row['class'], 'Debug')) {
                    continue;
                }
                if (str_contains($row['class'], 'Monolog')) {
                    if (str_contains($row['function'], 'logger')) {
                        $calledFile = self::returnTrace('file', $row);
                        $calledLine = self::returnTrace('line', $row);
                    }
                    if (str_contains($row['function'], 'trace')) {
                        $calledFile = self::returnTrace('file', $row);
                        $calledLine = self::returnTrace('line', $row);
                    }
                    if (str_contains($row['function'], 'LogStart')) {
                        $calledFile = self::returnTrace('file', $row);
                        $calledLine = self::returnTrace('line', $row);
                        $TraceList = $calledFile.':'.$calledLine;
                        break;
                    }
                    continue;
                }
                if ('' != $row['class']) {
                    $class = self::returnTrace('class', $row);
                }
            }
            if (str_contains($row['function'], 'require')) {
                continue;
            }
            // if (str_contains($row['function'], 'utminfo')) {
            //     continue;
            // }
            if ($row['function']) {
                $function = self::returnTrace('function', $row);
            }
            $TraceList = $calledFile.':'.$class.':'.$function.':'.$calledLine;
            break;
        }
        //  $TraceList = str_pad($TraceList, 100, '.');

        return $TraceList;
    }

    private static function getClassPath($class, $level = 1)
    {
        preg_match('/.*\\\\([A-Za-z]+)\\\\([A-Za-z]+)/', $class, $out);
        if (2 == $level) {
            return $out[1].'\\'.$out[2];
        }

        return $out[2];
    }

    private static function returnTrace($type, $row)
    {
        if ($row[$type]) {
            $text = $row[$type];

            if ('class' == $type) {
                $text = self::getClassPath($text, 2);
            }

            if ('file' == $type) {
                $text = basename($text);
            }

            $text = substr($text, 0, self::$padding[$type]);
            $text = str_pad($text, self::$padding[$type], ' ');

            // return UTMLog::formatPrint($text, self::$color[$type]);
            return $text;
        }

        return null;
    }
}
