<?php


namespace Bsodergren\utm;


class Template
{

    public static $static_html;
    public $html = '';
    public $header_html = '';
    public $default_params = [];
    public $template;
    private $test = 0;


    public function __construct()
    {
    }

    public static function GetHTML($template = '', $array = [])
    {
        $template_obj = new Template();
        $template_obj->template($template, $array);
        return $template_obj->html;
    }

    public static function echo($template = '', $array = [])
    {
        $template_obj = new Template();
        $template_obj->template($template, $array);
        echo $template_obj->html;
    }


    public function callback_replace($matches)
    {

        return "";
    }

    public function clear()
    {
        $this->html = '';
    }

    public function return($template = '', $array = [])
    {
        if ($template) {
            $this->template($template, $array);
        }

        $html = $this->html;
        $this->clear();
        return $html;
    }

    public function render($template = '', $array = [])
    {
        if ($template) {
            $this->template($template, $array);
        }

        $html = $this->html;
        $this->clear();
        echo $html;
    }

    private function loadTemplate($template)
    {
        $template = str_replace(".html", "", $template);

        $template_file = __TEMPLATE_DIR__ . "/" . $template . ".html";

        if (!file_exists($template_file)) {
            //use default template directory
            $template_text = "<h1>NO TEMPLATE FOUND<br>";
            $template_text .= "FOR <pre>" . $template . "</pre></h1> <br>";
        } else {

            $template_text = file_get_contents($template_file);
        }
        return $template_text;
    }

    private function defaults($text)
    {

        preg_match_all('/%%([A-Z_]+)%%/m', $text, $output_array);
        $params = [];

        foreach ($output_array[1] as $n => $def) {
            if (MediaSettings::isSet($def)) {
                $params[$def] = constant($def);
            }
        }
        $this->default_params = $params;
    }

    private function parse($text, $params = [])
    {
        $this->defaults($text);
        $params = array_merge($params, $this->default_params);
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $key = "%%" . strtoupper($key) . "%%";
                $text = str_replace($key, $value, $text);
            }

            $html = preg_replace_callback('|%%(\w+)%%|i', array($this, "callback_replace"), $text);
        }
        return $html;
    }

    public function template($template, $params = [])
    {
        $template_text = $this->loadTemplate($template);
        $html = $this->parse($template_text, $params);

        //$html = "\n<!-- start $template -->\n" . $html . "\n";
        $this->add($html);
        return $html;
    }


    public function add($var)
    {
        if (is_object($var)) {
            $this->html .= $var->html;
        } else {
            $this->html .= $var;
        }
    }
}

class MediaError extends Template
{

    public static function msg($severity, $msg = "", $refresh = 5)
    {
        $url = "/index.php";
        $timeout = $refresh;

        if(is_array($refresh))
        {
            $timeout = 0;
            if(key_exists('url',$refresh))
            {
                $url = $refresh['url'];
            }

            if(key_exists('timeout',$refresh))
            {
                $timeout = $refresh['timeout'];
            }
        }

        if ($msg != '') {
            include_once __LAYOUT_HEADER__;
            Template::echo("error/" . $severity, ['MSG' => $msg]);
        }

        htmldisplay::javaRefresh($url,$timeout);
        exit;
    }
}



class Header extends Template
{

    public static function display($template = "", $params = [])
    {

        $path = "/" . __SCRIPT_NAME__;
        if (MediaSettings::isTrue('__FORM_POST__')) {
            $path = "/" . __FORM_POST__;
        }

            if (file_exists(__TEMPLATE_DIR__ . $path . "/javascript.html")) {
                define('__CUSTOM_JS__', Template::GetHTML($path . "/javascript"));
            }

            if (file_exists(__TEMPLATE_DIR__ . $path . "/onload.html")) {
                define('__ONLOAD__', Template::GetHTML($path . "/onload"));
            }
        if (!MediaSettings::isTrue('NO_NAV')) {
            $params['__NAVBAR__'] = Navbar::Display();
        }

        $templateObj = new template();
        echo $templateObj->template("base/header/header", $params);
    }
}

class Footer extends Template
{
    //public $html;

    public static function display($template = '', $params = [])
    {
        $templateObj = new template();
        if (MediaSettings::isTrue('__SHOW_DEBUG_PANEL__')) {
            $errorArray = getErrorLogs();
            $debug_nav_link_html = '';

            foreach ($errorArray as $k => $file) {
                $file = basename($file);
                $key = str_replace(".", "_", basename($file));
                $debug_nav_links = [
                    'DEBUG_NAV_LINK_URL' => 'debug.php?log=' . $key . '',
                    'DEBUG_NAV_LINK_FILE' => $file
                ];
                $debug_nav_link_html .= $templateObj->template("base/footer/nav_item_link", $debug_nav_links);
            }
            $debug_panel_params['DEBUG_FILE_LIST'] = $debug_nav_link_html;

            $params['DEBUG_PANEL_HTML'] = $templateObj->template("base/footer/debug_panel", $debug_panel_params);
        }

        echo $templateObj->template("base/footer/footer", $params);
    }
}

class Navbar extends Template
{

    public static function display($template = '', $params = [])
    {
        $templateObj = new template();

        $nav_link_html = '';

        $nav_links_array = json_decode(__NAVBAR_LINKS__);
        foreach ($nav_links_array as $text =>  $url) {
            $nav_link_html .= $templateObj->template("base/navbar/navbar_item_link", ['NAV_LINK_URL' => $url, 'NAV_LINK_TEXT' => $text]);
        }

        $params['NAV_BAR_LINKS'] = $nav_link_html;

        return $templateObj->template("base/navbar/navbar", $params);
    }
}
