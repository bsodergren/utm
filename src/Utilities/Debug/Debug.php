<?php
/**
 * UTM Common classes
 */

namespace UTM\Utilities\Debug;

use UTM\Utilities\Colors;
use Nette\Utils\FileSystem;
use UTM\Bundle\Monolog\UTMLog;

class Debug
{
    public static $AppRootDir;
    public static $AppTraceDir;
    public static $RealTimeLog = false;

    public static $PrettyLogs  = true;
    public static $InfoArray   = [];
    public static $DebugArray  = [];

    private static $padding    = [
        'file'     => 20,
        'class'    => 22,
        'method'   => 20,
        'line'     => 4,
    ];

    private static $color      = [
        'file'     => ['red'],
        'class'    => ['yellow'],
        'function' => ['blue'],
        'line'     => ['green'],
    ];
    /**
     * Summary of traceFile
     * @param mixed $filename
     * @return string
     */
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

    // public static function findTextByValueInArray($fooArray, $searchValue)
    // {
    //     // self::file_append_file("---------------------------------------------------------",'arrayItems.txt');

    //     // self::file_append_file($searchValue,'arrayItems.txt');
    //     foreach ($fooArray as $index => $bar) {
    //         // self::file_append_file($bar['file'],'arrayItems.txt');
    //         if ($bar['file'] == $searchValue) {
    //             // self::file_append_file("MATCHED AT ".$index,'arrayItems.txt');
    //             return $index; // ['text'];
    //         }
    //     }

    //     return false;
    // }

    public static function __callStatic($name, $arguments)
    {
        $info               = self::getMethod();
        if ($info !== null) {

            $array = ["info" => $info, "Args" => self::cleanObjectArg($arguments)];
            if ($name == 'debug') {
                if (true === self::$RealTimeLog) {
                    self::writedump([$array], __SCRIPT_NAME__ . '_debug.log', false);
                } else {
                    self::$DebugArray[] = $array;
                }
            } elseif ($name == 'info') {
                if (true === self::$RealTimeLog) {
                    self::writedump([$array], __SCRIPT_NAME__ . '_trace.log', false);
                } else {
                    self::$InfoArray[] = $array;
                }
            }
            return 0;
        }

        if ($name == 'ddump') {
            utmdump($arguments);
            return 0;
        }
    }


    private static function cleanObjectArg($args)
    {


        array_walk_recursive(
            $args,
            function (&$value) {
                if (is_object($value)) {
                    $value = ["Class" => get_class($value), "Properties" => get_class_vars(get_class($value))];
                }
            },
        );

        return $args;
        // foreach($args as $arg){
        //     utmdump($arg);
        //     if(is_object($arg)){
        //         $value = get_class($arg);

        //     } else {
        //         $value = $arg;
        //     }
        //     $argArray[] = $value;
        // }

        // return $argArray;

    }
    public static function print_info($array)
    {
        self::printDump($array);
    }
    public static function print_debug($array)
    {
        self::printDump($array);
    }
    public static function write_info($array)
    {
        self::writedump($array, __SCRIPT_NAME__ . '_trace.log');
    }
    public static function write_debug($array)
    {
        self::writedump($array, __SCRIPT_NAME__ . '_debug.log');
    }

    private static function colorString($string, $color, $useColor = false)
    {
        if ($useColor === true) {
            return (new Colors())->getColoredString($string, $color);
        }
        return $string;
    }
    private static function getDumpInfo($value, $colors = true)
    {
        $print_args = [];
        foreach ($value as $row => $data) {

            foreach ($data['info'] as $key => $val) {
                if ('file' == $key) {

                    $file_a = explode("::", $val);
                    $file   =  self::colorString(basename($file_a[0]), 'blue', $colors);
                    $file   =  self::colorString(dirname($file_a[0]) . DIRECTORY_SEPARATOR, 'yellow', $colors) . $file;
                    $line   =  self::colorString($file_a[1], 'green', $colors);
                }
                if ('method' == $key) {
                    $funs   = explode(":", $val);

                    $method = '';
                    if (array_key_exists(1, $funs)) {
                        $method = self::colorstring($funs[1], 'red', $colors);
                    }
                }
                if ('time' == $key) {
                    $time = self::colorstring($val, 'light_blue', $colors);
                }
            }

            if (is_array($data['Args'])) {
                $args = self::cleanArgs($data['Args']);
            }

            $string           = implode(":", [$row, $time, $file, $method, $line]);
            $string           =  $string . '||' . $args;
            $print_args[$row] = $string;
        }

        return $print_args;
    }

    public static function printDump($value)
    {
        $lines = [];
        foreach (self::getDumpInfo($value, false) as $line) {
            $line    = str_replace(" ", "", $line);
            $line    = str_replace("\n", "", $line);
            $line    = str_replace("||", "\n   ", $line);

            $lines[] = $line . PHP_EOL;
        }

        $print = implode("\n", $lines);
        utmdump($print);
    }



    public static function writedump($value, $LogFile = null, $rotate = true)
    {

        $filename = self::traceFile($LogFile);


        if (file_exists($filename)) {
            if ($rotate === true) {
                unlink($filename);
            }
            $newstring = str_repeat("_", 80);
            $newstring = $newstring . PHP_EOL . str_repeat("_", 80);
            self::file_append_file($newstring, $filename);
            //

        }

        foreach (self::getDumpInfo($value, self::$PrettyLogs) as $string) {
            $string = str_replace("||", "\n", $string);

            self::file_append_file($string, $filename);
        }
    }

    // public static function print_array($array, $die = 0)
    // {
    //     print_r($array);
    //     if (1 == $die) {
    //         exit(\PHP_EOL);
    //     }
    // }

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
                [$class, $method] = explode(':', $classPath);
                $class            = str_replace('\\', '_', $class);
                $path[$class][]   = $method;
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

        $arguments = (new PrettyArray())->print($args);

        $lines     = [];

        $argarray  = explode("\n", $arguments);

        foreach ($argarray as $line) {
            if (!str_contains($line, '=>')) {
                $line    = str_replace("[", "", $line);
                $line    = str_replace("]", "", $line);
                $line    = str_replace(",", "]", $line);
            }

            if ($line == "") {
                continue;
            }

            $lines[] = $line;
        }

        $arguments = rtrim(implode("\n", $lines));

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
                return [
                    'file'      => '',
                    'method'    => 'utmshutdown',
                    'time'      => self::cleanTime(TimerNow()),
                ];
                continue;
            }

            if (
                str_contains($trace[$i]['function'], 'utminfo')
                || str_contains($trace[$i]['function'], 'utmdebug')
            ) {
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

                return [
                    'file'      => $calledFile . '::' . $calledLine,
                    'method'    => $class . $function,
                    'time'      => $timer,
                    // 'arguments' => $arguments
                ];
            }
        }
    }

    // public static function CallingFunctionName()
    // {
    //     $trace      = debug_backtrace();
    //     $TraceList  = '';

    //     $class      = str_pad('', self::$padding['class'], ' ');
    //     $calledFile = str_pad('', self::$padding['file'], ' ');
    //     $calledLine = str_pad('', self::$padding['line'], ' ');
    //     $function   = str_pad('', self::$padding['function'], ' ');

    //     foreach ($trace as $key => $row) {
    //         if (\array_key_exists('class', $row)) {
    //             if (str_contains($row['class'], 'Debug')) {
    //                 continue;
    //             }
    //             if (str_contains($row['class'], 'Monolog')) {
    //                 if (str_contains($row['function'], 'logger')) {
    //                     $calledFile = self::returnTrace('file', $row);
    //                     $calledLine = self::returnTrace('line', $row);
    //                 }
    //                 if (str_contains($row['function'], 'trace')) {
    //                     $calledFile = self::returnTrace('file', $row);
    //                     $calledLine = self::returnTrace('line', $row);
    //                 }
    //                 if (str_contains($row['function'], 'LogStart')) {
    //                     $calledFile = self::returnTrace('file', $row);
    //                     $calledLine = self::returnTrace('line', $row);
    //                     $TraceList  = $calledFile . ':' . $calledLine;
    //                     break;
    //                 }
    //                 continue;
    //             }
    //             if ('' != $row['class']) {
    //                 $class = self::returnTrace('class', $row);
    //             }
    //         }
    //         if (str_contains($row['function'], 'require')) {
    //             continue;
    //         }
    //         if ($row['function']) {
    //             $function = self::returnTrace('function', $row);
    //         }

    //         $TraceList = $calledFile . ':' . $class . ':' . $function . ':' . $calledLine;
    //         break;
    //     }
    //     //  $TraceList = str_pad($TraceList, 100, '.');

    //     return $TraceList;
    // }

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
