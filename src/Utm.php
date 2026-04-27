<?php

namespace UTM;

use Camoo\Config\Config;
use Dotenv\Dotenv;
use SaliBhdr\DumpLog\Factory\Logger;
use UTM\Utilities\Utilities;

class Utm
{
    public static $SHOW_HTML_DUMP = false;

    public static $UTM_CONFIG = [];

    public static $LOG_DIR = __DIR__ . '/logs';

    public static $LOG_STYLE = 'pretty';

    private static $logger;

    public static $SQL_TABLE_DIR;

    public static $SQL_UPDATE_DIR;

    public static $SQL_TABLE_PREFIX;

    public static $SQL_DATABASE;

    public static $SQL_USERNAME;

    public static $SQL_PASSWORD;

    public static $SQL_HOSTNAME;

    private $rotateLogs = true;

    public function __construct($logdir = null)
    {
        if ($logdir !== null) {
            self::$LOG_DIR = $logdir;
        }
        $this->rotateLogs();
        self::$logger = Logger::make(self::$LOG_STYLE)->path(self::$LOG_DIR);
    }

    public function rotateLogs()
    {
        if (array_key_exists('rotateLogs', self::$UTM_CONFIG)) {
            $this->rotateLogs = self::$UTM_CONFIG['rotateLogs'];
        }
        if ($this->rotateLogs === true) {
            $logs = Utilities::get_filelist(self::$LOG_DIR, daysOld: 2);
            if (count($logs) > 0) {
                foreach ($logs as $file) {
                    @unlink($file);
                }
            }
        }
    }

    public static function loadConifg($file)
    {
        self::$UTM_CONFIG = (new Config($file))->all();
    }

    public static function LoadEnv($directory = '')
    {
        if (! is_dir($directory)) {
            if (defined('__COMPOSER_DIR__')) {
                $directory = dirname(__COMPOSER_DIR__, 1);
            } else {
                return false;
            }
        }

        $fp = @fsockopen('tcp://127.0.0.1', 9912, $errno, $errstr, 1);
        if (! $fp) {
            $env_file = '.env';
        } else {
            $env_file = '.env-server';
        }

        return Dotenv::createUnsafeImmutable($directory, $env_file);
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
                break;
            default:
                if (\method_exists(self::class, $method)) {
                    self::$method($args);
                }
                break;
        }
    }

    public static function firstRun($dbType = 'mysql', $options = [])
    {
        self::$SQL_TABLE_DIR    = $options['table_dir'];
        self::$SQL_UPDATE_DIR   = $options['update_dir'];
        self::$SQL_TABLE_PREFIX = $options['prefix'];

        self::$SQL_USERNAME = $options['username'];
        self::$SQL_PASSWORD = $options['password'];
        self::$SQL_HOSTNAME = $options['hostname'];
        self::$SQL_DATABASE = $options['database'];
    }
}
// require_once __DIR__.'/Resources/Options.php';
