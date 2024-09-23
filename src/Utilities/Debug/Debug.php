<?php
/**
 *
 *   Plexweb
 *
 */

namespace UTM\Utilities\Debug;

use Nette\Utils\FileSystem;
use UTM\Bundle\Monolog\UTMLog;

class Debug
{
    public static $AppRootDir;
    public static $AppTraceDir;
    public static $InfoArray  = [];
    public static $DebugArray = [];

    private static $padding   = [
        'file'     => 20,
        'class'    => 22,
        'function' => 16,
        'line'     => 4,
    ];

    private static $color     = [
        'file'     => ['red'],
        'class'    => ['yellow'],
        'function' => ['blue'],
        'line'     => ['green'],
    ];

    private static function traceFile($filename)
    {
        $root      = self::rootPath();
        if (null !== self::$AppTraceDir) {
            $root = self::$AppTraceDir;
        }
        FileSystem::createDir($root);

        $filename  = basename($filename);
        $traceFile = $root . DIRECTORY_SEPARATOR . $filename;

        return $traceFile;
    }

    private static function rootPath()
    {
        if (array_key_exists('CONTEXT_DOCUMENT_ROOT', $_SERVER)) {
            $root = $_SERVER['CONTEXT_DOCUMENT_ROOT'];
        } else {
            $root = $_SERVER['SCRIPT_FILENAME'];
        }
        if (null !== self::$AppRootDir) {
            $root = self::$AppRootDir;
        }

        $root = \dirname(realpath($root), 1) . DIRECTORY_SEPARATOR;

        return $root;
    }

    private static function file_append_file($string, $name = 'tracefile.txt')
    {
        if (\is_array($string)) {
            $string = print_r($string, 1);
        }
        $file = self::traceFile($name);

        $fp   = fopen($file, 'a+');
        fwrite($fp, $string . \PHP_EOL);
        fclose($fp);
    }

    public static function findTextByValueInArray($fooArray, $searchValue)
    {
        // self::file_append_file("---------------------------------------------------------",'arrayItems.txt');

        // self::file_append_file($searchValue,'arrayItems.txt');
        foreach ($fooArray as $index => $bar) {
            // self::file_append_file($bar['file'],'arrayItems.txt');
            if ($bar['file'] == $searchValue) {
                // self::file_append_file("MATCHED AT ".$index,'arrayItems.txt');
                return $index; // ['text'];
            }
        }

        return false;
    }

    public static function debug(mixed ...$vars)
    {
        $info = self::getMethod();

        // $currentKey = false;

        if (null !== $info) {
            // $currentKey = self::findTextByValueInArray(self::$InfoArray, $info['file']);

            // if (false !== $currentKey) {
            //     $currentValue = self::$InfoArray[$currentKey]['arguments'];

            //     if (\is_string($currentValue)) {
            //         unset(self::$InfoArray[$currentKey]['arguments']);
            //         self::$InfoArray[$currentKey]['arguments'] = [$currentValue, $info['arguments']];
            //     } else {
            //         self::$InfoArray[$currentKey]['arguments'][] = $info['arguments'];
            //     }
            // } else {
            self::$DebugArray[] = ["info"=>$info,"Args"=>$vars];
            // }
        }
        // self::$InfoArray[] = ['page' => $caller, 'Data' => $var];
    }


    public static function info(mixed ...$vars)
    {
        $info = self::getMethod();
        // $currentKey = false;

        if (null !== $info) {
            // $currentKey = self::findTextByValueInArray(self::$InfoArray, $info['file']);

            // if (false !== $currentKey) {
            //     $currentValue = self::$InfoArray[$currentKey]['arguments'];

            //     if (\is_string($currentValue)) {
            //         unset(self::$InfoArray[$currentKey]['arguments']);
            //         self::$InfoArray[$currentKey]['arguments'] = [$currentValue, $info['arguments']];
            //     } else {
            //         self::$InfoArray[$currentKey]['arguments'][] = $info['arguments'];
            //     }
            // } else {
            self::$InfoArray[] = ["info"=>$info,"Args"=>$vars];
            // }
        }
        // self::$InfoArray[] = ['page' => $caller, 'Data' => $var];
    }

    public static function ddump($array)
    {
        utmdump($array);
    }

    public static function writedump($value, $file)
    {
        $filename = self::traceFile($file);
        if (file_exists($filename)) {
            $string = str_repeat("_", 36);
            self::file_append_file($string, $filename);
            //unlink($filename);
        }

        //  utmdd($value);
        foreach ($value as $row => $data) {
            foreach ($data['info'] as $key => $val) {

                if ('arguments' == $key) {

                    $args = $val;
                }
                if ('file' == $key) {
                    $file_a = explode("::", $val);
                    $file   = $file_a[0];
                    $line   = $file_a[1];
                }
                if ('method' == $key) {
                    $funs   = explode(":", $val);
                    $method = $funs[1];
                }
                if ('time' == $key) {
                    $time = $val;
                }
                // $val = str_replace("\n","",$val);

                // utmdump([$row=>[$key,$val]]);
            }
            if (is_array($data['Args'])) {
                // $val = print_r($val, 1);
                $args = self::cleanArgs($data['Args']);
                // $val = 'array';
            }
            $string = '[' . $row . '][' . $time . '][' . $file . ':' . $method . ':' . $line . ']' . $args;
            self::file_append_file($string, $filename);
        }
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
        $trace      = debug_backtrace();
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
                                $arg[] = "'" . $value . "'";
                            }
                        }
                    }
                }
                $arguments    = implode(',', $arg);
                $classArray[] = self::getClassPath($row['class'], 2) . ':' . $row['function'] . '(' . $arguments . ')';
            }
        }

        if (\count($classArray) > 0) {
            $classArray = array_reverse($classArray);
            foreach ($classArray as $k => $classPath) {
                [$class,$method] = explode(':', $classPath);
                $class           = str_replace('\\', '_', $class);
                $path[$class][]  = $method;
            }

            foreach ($path as $classPath => $methods) {
                $classPath  = str_replace('_', '\\', $classPath);
                if (\is_array($methods)) {
                    $level      = 4;
                    $spaces     = str_repeat(' ', $level * 4);
                    $methodPath = implode("\n" . $spaces . '->', $methods);
                }
                $fullPath[] = $classPath . ':' . $methodPath;
            }
            $level      = 1;
            $spaces     = str_repeat(' ', $level * 4);

            return "\n" . implode("\n" . $spaces . '->', $fullPath);
        }

        return '';
    }

    private static function cleanArgs($args)
    {
        //         if(is_array($args)) {
        // if( array_key_exists(0,$args))
        // {
        //     $args = $args[0];
        // }}
        $arguments = (new PrettyArray())->print($args, 1);

        // self::file_append_file($arguments,"artlist.txt");

        // $arguments    = str_replace(["\t", "\n"], '',   $arguments);

        // return $arguments;

        // $arguments    = str_replace(',]', ']', $arguments);
        // // $arguments    = str_replace("[[","",$arguments);

        // if (str_contains($arguments, '=>')) {
        //     $arguments    = str_replace('[[', "[[\n   ", $arguments);
        //     $arguments    = str_replace("',", "'\n   ", $arguments);
        //     $arguments    = str_replace(']]', "\n]]", $arguments);
        // }

        return $arguments;
    }

    public static function cleanTime($time)
    {
        $find    = 'default/export-data: ';
        $replace = '';

        return str_replace($find, $replace, $time);
    }

    public static function getMethod()
    {
        $root  = self::rootPath();

        $trace = debug_backtrace();
        $class = '';
        $arg   = [];
        // utmdump($trace);
        for ($i = 0; $i < \count($trace); ++$i) {
            if (array_key_exists('file', $trace[$i])) {
                if (str_contains($trace[$i]['file'], 'vendor')) {
                    continue;
                }
                // continue;
            }
            if (str_contains($trace[$i]['function'], 'utmshutdown')) {
                return ['file'  => '',
                    'method'    => 'utmshutdown',
                    'time'      => self::cleanTime(TimerNow()),
                    'arguments' => ''];
                continue;
            }

            if (str_contains($trace[$i]['function'], 'utminfo')
            || str_contains($trace[$i]['function'], 'utmdebug')) {
                $calledFile = $trace[$i]['file'];
                $calledLine = $trace[$i]['line'];
                $function   = $trace[$i]['function'];
                $args       = $trace[$i]['args'];
                if (\array_key_exists($i + 1, $trace)) {
                    if (\array_key_exists('class', $trace[$i + 1])) {
                        $class    = $trace[$i + 1]['class'] . ':';
                        $function = $trace[$i + 1]['function'];

                        //  $args = $trace[$i+1]['args'];
                    }
                }

                $arguments  = self::cleanArgs($args);
                $timer      = self::cleanTime(TimerNow());

                $calledFile = str_replace($root, '', $calledFile);

                return ['file'  => $calledFile . '::' . $calledLine,
                    'method'    => $class . $function,
                    'time'      => $timer,
                    // 'arguments' => $arguments
                ];
            }
        }
    }

    public static function CallingFunctionName()
    {
        $trace      = debug_backtrace();
        $TraceList  = '';

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
                        $TraceList  = $calledFile . ':' . $calledLine;
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

            $TraceList = $calledFile . ':' . $class . ':' . $function . ':' . $calledLine;
            break;
        }
        //  $TraceList = str_pad($TraceList, 100, '.');

        return $TraceList;
    }

    private static function getClassPath($class, $level = 1)
    {
        preg_match('/.*\\\\([A-Za-z]+)\\\\([A-Za-z]+)/', $class, $out);
        if (2 == $level) {
            return $out[1] . '\\' . $out[2];
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
