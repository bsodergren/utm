<?php
/**
 * Command like Metatag writer for video files.
 */

namespace UTM\Utils;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Option.
 */
class Option extends InputOption
{
    private static $options = null;

    private static $cmdOptions = [];

    /**
     * init.
     *
     * @param mixed $input
     */
    public static function init(InputInterface $input)
    {
        if(self::$options === null){
            self::$options = $input->getOptions();
        }
    }

    public static function getOptions()
    {

        if (0 == \count(self::$cmdOptions)) {
            foreach (self::$options as $option => $value) {
                if (\is_array($value)) {
                    if (\count($value) > 0) {
                        self::$cmdOptions[$option] = $value;
                    }
                } else {
                    if (null !== $value) {
                        if (false != $value) {
                            self::$cmdOptions[$option] = $value;
                        }
                    }
                }
            }
        }

        return self::$cmdOptions;
    }

    public static function getValue($name, $return = false)
    {
        $result = null;
        if (\array_key_exists($name, self::$options)) {
            
            $value = self::$options[$name];
            if (!\is_array($value)) {
                if (str_contains($value, ',')) {
                    $value = explode(',', $value);
                    $result = self::valueIsArray($value, $name);
                } else {
                    $result = self::valueIsString($value, $name);
                }
            } else {
                $result = self::valueIsArray($value, $name);
            }

            if (\is_array($result)) {
                if (true == $return) {
                    $result = $result[0];
                }
            }
        }

        return $result;
    }

    private static function ispath($text, $name)
    {
        if ('filelist' == $name) {
            return realpath($text);
        }

        return $text;
    }

    private static function valueIsArray($value, $name)
    {
        foreach ($value as $text) {
            if (str_contains($text, ',')) {
                $textArray = explode(',', $text);
                foreach ($textArray as $ttext) {
                    if ('' != $ttext) {
                        $ret[] = self::ispath($ttext, $name);
                    }
                }
            } else {
                $ret[] = self::ispath($text, $name);
            }
        }

        return $ret;
    }

    private static function valueIsString($value, $name)
    {
        return $value;
    }

    public static function isTrue($name)
    {
        if (\array_key_exists($name, self::$options)) {
            if (\is_array(self::$options[$name])) {
                if (\count(self::$options[$name]) > 0) {
                    return true;
                }

                return false;
            }
            if (null !== self::$options[$name]) {
                return self::$options[$name];
            }
        }

        return null;
    }

    // public static function dump($loc, ...$val)
    // {
    //     if (self::isTrue('dump')) {
    //         if ($loc == self::getValue('dump')) {
    //             utmdd([__METHOD__,$val]);
    //         }
    //     }
    // }
}
