<?php
/**
 * Command like Metatag writer for video files.
 */

namespace UTM;

use Dotenv\Dotenv;
use SaliBhdr\DumpLog\Factory\Logger;

class Utm
{
    public static $SHOW_HTML_DUMP = false;
    public static $LOG_DIR        = __DIR__ . '/logs';
    public static $LOG_STYLE      = 'pretty';
    private static $logger;

    public function __construct($logdir = null)
    {
        if (null !== $logdir) {
            self::$LOG_DIR = $logdir;
        }

        self::$logger = Logger::make(self::$LOG_STYLE)->path(self::$LOG_DIR);
    }

    public static function LoadEnv($directory = '')
    {
        if (!is_dir($directory)) {
            if (defined('__COMPOSER_DIR__')) {
                $directory = dirname(__COMPOSER_DIR__, 1);
            } else {
                return false;
            }
        }

        $fp = @fsockopen('tcp://127.0.0.1', 9912, $errno, $errstr, 1);
        if (!$fp) {
            $env_file = '.env';
        } else {
            $env_file = '.env-server';
        }

        return Dotenv::createUnsafeImmutable($directory, $env_file)->load();
    }

    public static function __callStatic($method, $args)
    {
        switch ($method) {
            case 'alert':
            case 'critical':
            case 'error':
            case 'warning':
            case 'notice':
            case 'info':
            case 'debug':
            case 'exception':
            case 'log':
                self::$logger->$method($args);
        }
    }
}
// require_once __DIR__.'/Resources/Options.php';
