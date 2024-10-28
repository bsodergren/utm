<?php
/**
 * UTM Common classes
 */

namespace UTM\Utilities;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Option.
 */
class Option extends InputOption
{
    private static $options;

    private static $cmdOptions = [];

    /**
     * init.
     *
     * @param mixed $input
     */
    public static function init(InputInterface $input, $options = null)
    {
        if (null === self::$options) {
            self::$options = $input->getOptions();
        }

        if (is_array($options)) {
            self::$options = array_merge(self::$options, $options);
        }
    }

    public static function set($name, $value)
    {
        self::$options = array_merge(self::$options, [$name => $value]);
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
                    $value  = explode(',', $value);
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
        $ret = null;
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

    public static function isFalse($name)
    {
        $val = self::isTrue($name);

        return !$val;
    }

    public static function isTrue($name)
    {
        if (defined($name)) {
            if (true == constant($name)) {
                return true;
            } else {
                return false;
            }
        }

        if (\is_bool($name)) {
            return $name;
        }
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
