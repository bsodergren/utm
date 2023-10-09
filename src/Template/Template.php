<?php
namespace UTM\Template;

use UTM\Utils\Utilities;
use UTM\Utils\UtmDevice;


class Template
{

    public static $static_html;
    public $html = '';
    public $header_html = '';
    public $default_params = [];
    public $templatefile;
    private $test = 0;
    private $TemplateLoc = '';

    public $error = false;

    static protected $StaticTemplateLoc = '';
    public function __construct($template_directory = '')
    {
        $this->TemplateLoc = $template_directory;
        self::$StaticTemplateLoc = $template_directory;
    }



    public static function GetHTML($template = '', $array = [], $error = true)
    {
        $template_obj = new self();
        $template_obj->error = $error;
        $template_obj->templatefile = $template;
        $template_obj->template($template, $array);

        return $template_obj->html;
    }

    public static function echo($template = '', $array = [], $error = true)
    {
        $template_obj = new self();
        $template_obj->error = $error;
        $template_obj->templatefile = $template;

        $template_obj->template($template, $array);
        echo $template_obj->html;
    }

    public function callback_replace($matches)
    {
        return '';
    }

    
    public function callback_include_html($matches)
    {

        $params = [];

        if(array_key_exists(3,$matches))
        {
            if(array_key_exists(4,$matches)){
                if($matches[4] == '')
                {
                 return '';
                }
                $params[$matches[3]] = $matches[4];
            }
        }

        $template_file = dirname($this->templatefile,1).DIRECTORY_SEPARATOR.$matches[1];
        $html = $this->return($template_file,$params);
        return $html;
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
        $template_file = UtmDevice::getTemplateFile($template);
        if (null !== $template_file) {
            return file_get_contents($template_file).\PHP_EOL;
        }

        if (true == $this->error) {
            $template_text = '<h1>NO TEMPLATE FOUND<br>';
            $template_text .= 'FOR <pre>'.$template_file.'</pre></h1> <br>';
        } else {
            $template_text = '';
        }
        //        $template_text = '<!-- END OF '.$template.'-->'.\PHP_EOL;

        return $template_text.\PHP_EOL;
    }

    private function defaults($text)
    {
        preg_match_all('/%%([A-Z_]+)%%/m', $text, $output_array);
        $params = [];

        foreach ($output_array[1] as $n => $def) {
            if (Utilities::isSet($def)) {
                $params[$def] = \constant($def);
            }
        }
        $this->default_params = $params;
    }

    private function parse($text, $params = [])
    {
        $this->defaults($text);
        $params = array_merge($params, $this->default_params);
        if (\is_array($params)) {
            foreach ($params as $key => $value) {
                $key = '%%'.strtoupper($key).'%%';
                $text = str_replace($key, $value, $text);
            }
            $text = preg_replace_callback('|%%(\w+)%%|i', [$this, 'callback_replace'], $text);
        }

       $text = preg_replace_callback('|{{(\w+)(,(\w+)=\|(.{0,})\|)?}}|i', [$this, 'callback_include_html'], $text);

        return $text;
    }

    public function template($template, $params = [])
    {
        $template_text = $this->loadTemplate($template);
        $html = $this->parse($template_text, $params);

        $this->add($html);

        return $html;
    }

    public function add($var)
    {
        if (\is_object($var)) {
            $this->html .= $var->html;
        } else {
            $this->html .= $var;
        }
    }


}
