<?php

namespace Bsodergren\utm;

class Colors
{

    private $foreground_colors = [];

    private $background_colors = [];

    private $fg_color;

    public function __construct()
    {
        // Set up shell colors
        $this->foreground_colors['black']        = '0;30';
        $this->foreground_colors['dark_gray']    = '1;30';
        $this->foreground_colors['blue']         = '0;34';
        $this->foreground_colors['light_blue']   = '1;34';
        $this->foreground_colors['green']        = '0;32';
        $this->foreground_colors['light_green']  = '1;32';
        $this->foreground_colors['cyan']         = '0;36';
        $this->foreground_colors['light_cyan']   = '1;36';
        $this->foreground_colors['red']          = '0;31';
        $this->foreground_colors['light_red']    = '1;31';
        $this->foreground_colors['purple']       = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown']        = '0;33';
        $this->foreground_colors['yellow']       = '1;33';
        $this->foreground_colors['light_gray']   = '0;37';
        $this->foreground_colors['white']        = '1;37';

        $this->background_colors['black']      = '40';
        $this->background_colors['red']        = '41';
        $this->background_colors['green']      = '42';
        $this->background_colors['yellow']     = '43';
        $this->background_colors['blue']       = '44';
        $this->background_colors['magenta']    = '45';
        $this->background_colors['cyan']       = '46';
        $this->background_colors['light_gray'] = '47';
    } //end __construct()

    public function getClassColor()
    {
        if (isset($this->foreground_colors[$this->fg_color])) {
            return 'color:' . $this->fg_color . ';';
        }
        return '';
    }

    public function getColoredDiv($html, $background_color)
    {

        $class_tag = '';
        if (isset($this->background_colors[$background_color])) {
            $class_tag = "class";
        }
    }

    // Returns colored string
    public function getColoredSpan($string, $foreground_color = null, $background_color = null)
    {
        $this->fg_color = $foreground_color;
        $colored_string = '<span style="' . $this->getClassColor() . '">' . $string . '</span>';;
        return $colored_string;
    } //end getColoredHTML()


    public function getColoredString($string, $foreground_color = null, $background_color = null)
    {
        $colored_string = '';

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . 'm';
        }

        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . 'm';
        }

        // Add string and end coloring
        $colored_string .= $string . "\033[0m";

        return $colored_string;
    } //end getColoredString()


    // Returns all foreground color names
    public function getForegroundColors()
    {
        return array_keys($this->foreground_colors);
    } //end getForegroundColors()


    // Returns all background color names
    public function getBackgroundColors()
    {
        return array_keys($this->background_colors);
    } //end getBackgroundColors()


} //end class

class utils
{

    public static function isTrue($define_name)
    {
        if (defined($define_name)) {



            if (constant($define_name) == true) {
                //  MediaUpdate::echo(constant($define_name));
                return 1;
            }
        }
        return 0;
    }

    public static function isSet($define_name)
    {
        if (defined($define_name)) {
            return 1;
        }
        return 0;
    }
    public static function toint($string)
	{
		
		$string_ret = str_replace(",","",$string);
		return $string_ret;
	}
}


