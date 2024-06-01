<?php
/**
 * Command like Metatag writer for video files.
 */

namespace UTM\Bundle\Monolog;

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use UTM\Utilities\Debug\Debug;
use UTM\Utilities\Debug\Timer;

class UTMLog
{
    public static $Logger;

    protected $logger;

    protected $disabled;

    protected static $DumpPadding = '';

    public static $Cols = 120;

    private static $_objects = [];

    private static $_output;

    public static $display = true;

    protected $infostream;

    protected $noticestream;

    /**
     * Summary of _depth.
     */
    private static $_depth;

    public function __construct($channel = 'default', $disabled = true)
    {
        // $this->disabled  = $disabled;
        // $channel = 'default';
        // $this->processor = new PsrLogMessageProcessor();
        // $LogFormat = ["%message% %context%\n",null,true];
        $LogFormat = ["[%datetime%][%level_name%] %message% %context%\n", 'g:i:s.v', true];
        $format = new UtmLineFormatter(...$LogFormat);
        $this->logger = new Logger($channel);

        //        ErrorHandler::register($this->logger);

        $log_file = __LOGFILE_DIR__.'/'.$channel.'.log';
        if (file_exists($log_file)) {
            file_put_contents($log_file, '');
        }
        $stream = new StreamHandler($log_file, Logger::INFO);
        $stream->setFormatter($format);
        $this->logger->pushHandler($stream);

        /*
                $info_file      = __LOGFILE_DIR__.'/'.$channel.'_info.log';
                if(file_exists($info_file))
                {
                    file_put_contents($info_file, "");
                }
                $infostream          = new StreamHandler($info_file, Logger::INFO);
                $infostream->setFormatter($format);
                $this->logger->pushHandler($infostream);


                $notice_file      = __LOGFILE_DIR__.'/'.$channel.'_notice.log';
                if(file_exists($notice_file))
                {
                    file_put_contents($notice_file, "");
                }
                $noticestream          = new StreamHandler($notice_file, Logger::NOTICE);
                $noticestream->setFormatter($format);
                $this->logger->pushHandler($noticestream);
            */

        if ($format->allowInlineLineBreaks) {
            self::$DumpPadding = ' ';
        }

        $this->logger->reset();
    }

    public static function __callStatic($method, $args)
    {
        if (method_exists(Timer::class, $method)) {
            $message = '';
            $context = null;

            if (\array_key_exists('0', $args)) {
                $message = $args[0];
            }
            if (\array_key_exists(1, $args)) {
                $context = $args[1];
            }

            Timer::$method($message, $context);
            self::logger($message, $context);
        }
    }

    /**
     * Method logger.
     *
     * @param $message $message [explicite description]
     */
    public static function logger($message, ...$args)
    {
        if (0 == \count($args)) {
            $context = '';
        } elseif (1 == \count($args)) {
            $context = $args[0];
        } else {
            foreach ($args as $i => $array) {
                $context[] = $array;
            }
        }

        self::$Logger->log(Logger::INFO, $message, $context);
    }

    public static function trace($message = '')
    {
        self::$Logger->log(Logger::INFO, $message, Debug::tracePath());
    }

    public static function LogStart($msg = 'Start Logging')
    {
        $message = str_pad('> '.$msg.' <', self::$Cols, '-', \STR_PAD_BOTH);
        self::$Logger->log(Logger::NOTICE, $message, null);
    }

    public static function LogEnd($msg = 'Exiting')
    {
        $message = str_pad('> '.$msg.' <', self::$Cols, '-', \STR_PAD_BOTH);
        self::$Logger->log(Logger::NOTICE, $message, null);
    }

    /**
     * Method log.
     *
     * @param $level   $level [explicite description]
     * @param $message $message [explicite description]
     * @param $context $context [explicite description]
     */
    protected function log($level, $message, $context)
    {
        $message = self::formatPrint(self::dump($message), ['blue']);

        $caller = Debug::CallingFunctionName();
        if ('' != $caller) {
            $caller .= ';';
        }

        $message = $caller.$message; // .PHP_EOL.PHP_TAB;

        if (null !== $context) {
            if ('' != $context) {
                $context = self::dump($context);
            } else {
                $context = null;
            }
        }
        $this->logger->addRecord($level, (string) $message, (array) $context);
    }

    /**
     * Method dump.
     *
     * @param $var       $var [explicite description]
     * @param $depth     $depth [explicite description]
     * @param $highlight $highlight [explicite description]
     */
    public static function dump($var, $depth = 4, $highlight = false)
    {
        self::$_output = '';
        // self::$_objects = [];
        self::$_depth = $depth;
        self::dumpInternal($var, 2);

        return self::$_output;
    }

    /**
     * Method dumpInternal.
     *
     * @param $var   $var [explicite description]
     * @param $level $level [explicite description]
     */
    private static function dumpInternal($var, $level)
    {
        switch (\gettype($var)) {
            case 'boolean':
                self::$_output .= $var ? 'true' : 'false';
                break;

            case 'integer':
                self::$_output .= "{$var}";
                break;

            case 'double':
                self::$_output .= "{$var}";
                break;

            case 'string':
                self::$_output .= "'{$var}'";
                break;

            case 'resource':
                self::$_output .= '{resource}';
                break;

            case 'NULL':
                self::$_output .= 'null';
                break;

            case 'unknown type':
                self::$_output .= '{unknown}';
                break;

            case 'array':
                if (self::$_depth <= $level) {
                    self::$_output .= \PHP_EOL.'array(...)';
                } elseif (empty($var)) {
                    self::$_output .= \PHP_EOL.'array()';
                } else {
                    $keys = array_keys($var);
                    $spaces = str_repeat(self::$DumpPadding, $level * 4);
                    self::$_output .= \PHP_EOL.'array (';

                    foreach ($keys as $key) {
                        self::$_output .= "\n".$spaces." [{$key}] => ";
                        self::dumpInternal($var[$key], $level + 1);
                    }

                    self::$_output .= "\n".$spaces.')';
                }
                break;

            case 'object':
                if (($id = array_search($var, self::$_objects, true)) !== false) {
                    self::$_output .= $var::class.'#'.($id + 1).'(...)';
                } elseif (self::$_depth <= $level) {
                    self::$_output .= $var::class.'(...)';
                } else {
                    $id = array_push(self::$_objects, $var);
                    $className = $var::class;
                    $members = (array) $var;
                    $keys = array_keys($members);
                    $spaces = str_repeat(self::$DumpPadding, $level * 4);
                    self::$_output .= "{$className}#{$id}\n".$spaces.'(';

                    foreach ($keys as $key) {
                        $keyDisplay = strtr(trim($key), ["\0" => ':']);
                        self::$_output .= "\n".$spaces." [{$keyDisplay}] => ";
                        self::dumpInternal($members[$key], $level + 1);
                    }

                    self::$_output .= "\n".$spaces.')';
                }
                break;
        }
    }

    public static function formatPrint(string $text = '', array $format = [])
    {
        if (false == self::$display) {
            return $text;
        }

        $codes = [
            'bold' => 1,
            'italic' => 3, 'underline' => 4, 'strikethrough' => 9,
            'black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37,
            'blackbg' => 40, 'redbg' => 41, 'greenbg' => 42, 'yellowbg' => 44, 'bluebg' => 44, 'magentabg' => 45, 'cyanbg' => 46, 'lightgreybg' => 47,
        ];
        $formatMap = array_map(function ($v) use ($codes) {
            return $codes[$v];
        }, $format);

        return "\e[".implode(';', $formatMap).'m'.$text."\e[0m";
    }
}
