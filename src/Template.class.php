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
    private $TemplateLoc = '';

    static protected $StaticTemplateLoc = '';
    public function __construct($template_directory)
    {
        $this->TemplateLoc = $template_directory;
        self::$StaticTemplateLoc = $template_directory;
    }

    public static function GetHTML($template = '', $array = [])
    {
        $template_obj = new Template(self::$StaticTemplateLoc);
        $template_obj->template($template, $array);
        return $template_obj->html;
    }

    public static function echo($template = '', $array = [])
    {
        $template_obj = new Template(self::$StaticTemplateLoc);
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

        $template_file = $this->TemplateLoc . "/" . $template . ".html";

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
            if (Utils::isSet($def)) {
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

class utmError extends Template
{

    public static function msg($severity, $msg = "", $refresh = 5)
    {
        $timeout = $refresh;
        $url = "";
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
           // include_once __LAYOUT_HEADER__;
            Template::echo("error/" . $severity, ['MSG' => $msg]);
        }

        htmldisplay::javaRefresh($url,$timeout);
        exit;
    }
}




class Header extends Template
{

    public static function display($params = [])
    {
        echo parent::GetHTML("/header/header", $params);
    }
}


class Footer extends Template
{
    //public $html;

    public static function display( $params = [])
    {
      
        echo parent::GetHTML("/footer/footer", $params);
    }
}



class Navbar extends Template
{

    public static function display($params = [])
    {
       
//        $params['NAVBAR_MENU_HTML'] = $navbar_menu_html;

        echo parent::GetHTML("/navbar/navbar", $params);
    }
}
