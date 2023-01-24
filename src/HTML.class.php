<?php

namespace Bsodergren\utm;
//use Nette\Utils\FileSystem;
//use Nette\Utils\DateTime;
use Bsodergren\utm\Template;

class HTML
{

    public static function javaRefresh($url, $timeout = 0)
    {

        if ($timeout > 0) {
            $timeout = $timeout * 1000;
            $update_inv =  $timeout / 100;
            Template::echo("progress_bar", ['SPEED' => $update_inv]);
        }

        Template::echo('js_refresh_window', ['REFRESH_URL' => $url, 'MS_SECONDS' => $timeout]);
    }

    public static function echo($value, $exit = 0)
    {

        echo '<pre>' . var_export($value, 1) . '</pre>';

        if ($exit == 1) {
            exit;
        }
    }
    
    public static function output($var,$nl="")
    {
        echo $var . $nl."\n";
        ob_flush();
    }


    
}