<?php

namespace UTM;

use SaliBhdr\DumpLog\Factory\Logger;

class Utm
{
    public static $SHOW_HTML_DUMP = false;
    public static $LOG_DIR = __DIR__.'/logs';
    public static $LOG_STYLE = 'pretty';
    private static $logger;

    public function __construct($logdir = null)
    {
        if (null !== $logdir) {
            self::$LOG_DIR = $logdir;
        }

        self::$logger = Logger::make(self::$LOG_STYLE)->path(self::$LOG_DIR);
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
