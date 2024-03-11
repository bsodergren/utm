<?php

namespace UTM\Template;
use UTM\Utils\UtmDevice;


class HTMLDocument
{
    public $nav_list_dir = 'list';
    public $template;

    public function __construct()
    {
        $this->template = new Template();
    }

    public static function __callStatic($method, $args)
    {
        $new = new self();
        $method = str_replace('_', '', $method);

        return $new->$method();
    }

    public function getNavbar()
    {
            return UtmDevice::getNavbar();
    
    }

    public function headerJS()
    {
        $path = '/'.__SCRIPT_NAME__;

        $js = trim(Template::GetHTML($path.'/javascript', [], false));

        $onload = trim(Template::GetHTML($path.'/onload', [], false));

        return [$js, $onload];
    }

    public function headerCSS()
    {
        $bootstrap = Template::GetHTML('base/header/bootstrap_5', [], false);
        $custom_css = Template::GetHTML('base/header/css', [], false);

        return [$bootstrap, $custom_css];
    }



    public static function displayMsg()
    {
        if (isset($GLOBALS)) {
            if (\is_array($GLOBALS['_REQUEST'])) {
                if (\array_key_exists('msg', $GLOBALS['_REQUEST'])) {
                    return Template::GetHTML('base/header/return_msg', ['MSG' => urldecode($GLOBALS['_REQUEST']['msg'])], false);
                }
            }
        }

        return '';
    }
}
