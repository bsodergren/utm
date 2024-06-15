<?php
/**
 *  Plexweb
 */

namespace UTM\Utilities\Debug;

use UTM\Bundle\Monolog\UTMLog;


class Debug
{
    public static $DebugArray = [];

    private static $padding = [
        'file'     => 20,
        'class'    => 22,
        'function' => 16,
        'line'     => 4,
    ];

    private static $color = [
        'file'     => ['red'],
        'class'    => ['yellow'],
        'function' => ['blue'],
        'line'     => ['green'],
    ];


    private static function file_append_file($string)
    {
        if (\is_array($string)) {
            $string = print_r($string, 1);
        }
        $file = \dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1).'/tracefile.txt';
        $fp   = fopen($file, 'a+');
        fwrite($fp, $string);
        fclose($fp);
    }

    public static function info(mixed ...$vars)
    {
        $info = self::getMethod();

        self::$DebugArray[] = $info;
        // self::$DebugArray[] = ['page' => $caller, 'Data' => $var];
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
        $trace = debug_backtrace();
        $classArray = [];
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
                $arguments    = implode(',', $arg);
                $classArray[] = self::getClassPath($row['class'], 2).':'.$row['function'].'('.$arguments.')';
            }
        }

        if(count($classArray) > 0){
        $classArray = array_reverse($classArray);
        foreach ($classArray as $k => $classPath) {
            [$class,$method] = explode(':', $classPath);
            $class           = str_replace('\\', '_', $class);
            $path[$class][]  = $method;
        }

        foreach ($path as $classPath => $methods) {
            $classPath = str_replace('_', '\\', $classPath);
            if (\is_array($methods)) {
                $level      = 4;
                $spaces     = str_repeat(' ', $level * 4);
                $methodPath = implode("\n".$spaces.'->', $methods);
            }
            $fullPath[] = $classPath.':'.$methodPath;
        }
        $level  = 1;
        $spaces = str_repeat(' ', $level * 4);

        return "\n".implode("\n".$spaces.'->', $fullPath);
    }
    return "";
    }

    public static function getMethod()
    {
        $root = \dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1);

        $trace = debug_backtrace();
        $class    = '';

        // self::file_append_file($trace);
        for ($i=0; $i < \count($trace); ++$i) {
            $arg = [];

            if (str_contains($trace[$i]['file'], 'vendor')) {
                continue;
            }

            // if (\array_key_exists('class', $trace[$i])) {
            //     if (str_contains($trace[$i]['class'], 'Symfony')) {
            //         continue;
            //     }
            //     if (str_contains($trace[$i]['class'], 'Debug')) {
            //         continue;
            //     }
            //     if (str_contains($trace[$i]['class'], 'Monolog')) {
            //         continue;
            //     }
            //     if (str_contains($trace[$i]['class'], 'Monolog')) {
            //         if (str_contains($trace[$i]['function'], 'logger')) {
            //             $calledFile = self::returnTrace('file', $trace[$i]);
            //             $calledLine = self::returnTrace('line', $trace[$i]);
            //         }
            //         continue;
            //     }
            //     // if(!isset($calledFile)) {
            //     // $calledFile = self::returnTrace('file', $row);
            //     // $calledLine = self::returnTrace('line', $row);
            //     // }
            // }

            // if (str_contains($trace[$i]['function'], 'require')) {
            //     continue;
            // }
            // if (str_contains($row['function'], 'utminfo')) {
            //     continue;
            // }

            if (str_contains($trace[$i]['function'], 'utminfo')) {
                $calledFile = $trace[$i]['file'];
                $calledLine = $trace[$i]['line'];
                $function   =  $trace[$i]['function'];

                if (\array_key_exists($i + 1, $trace)) {
                    if (\array_key_exists('class', $trace[$i + 1])) {
                        $class    =  $trace[$i + 1]['class'].':';
                        $function =  $trace[$i + 1]['function'];
                    }
                }

                // continue;

                if (\array_key_exists('args', $trace[$i])) {
                    $args = $trace[$i]['args'];
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

                $arguments    = implode(',', $arg);
                $calledFile   = str_replace($root, '', $calledFile);

                $classArray = ['file' => $calledFile.'::'.$calledLine, 'method'=>$class.$function, 'arguments'=>'('.$arguments.')'];
                // self::$stopwatch->lap(self::$stopWatchName);
                // self::dump($calledFile.'::'.$calledLine . "->".$class.$function);
               // return implode("\n", $classArray);
               return $classArray;
            }
        }
    }

    public static function CallingFunctionName()
    {
        $trace     = debug_backtrace();
        $TraceList = '';

        $class      = str_pad('', self::$padding['class'], ' ');
        $calledFile = str_pad('', self::$padding['file'], ' ');
        $calledLine = str_pad('', self::$padding['line'], ' ');
        $function   = str_pad('', self::$padding['function'], ' ');

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
                        $TraceList  = $calledFile.':'.$calledLine;
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

            return UTMLog::formatPrint($text, self::$color[$type]);
        }

        return null;
    }
}
