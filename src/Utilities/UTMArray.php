<?php
/**
 * Command like Metatag writer for video files.
 */

namespace UTM\Utilities;

/**
 * Summary of MediaArray.
 */
class UTMArray
{
    /**
     * Summary of diff.
     *
     * @return array
     */
    public static function diff($array, $compare, $diff = 'key')
    {
        $return_array = [];
        if ('key' == $diff) {
            foreach ($array as $key => $value) {
                if (!\array_key_exists($key, $compare)) {
                    $return_array[$key] = $value;
                }
            }
        } else {
            $return_array = array_diff($array, $compare);
        }

        return $return_array;
    }

    /**
     * Summary of search.
     */
    public static function search($arr, $string, $key = '', $exact = false)
    {
        $ret = array_filter($arr, function ($value) use ($string, $exact, $key) {
            if (\is_array($value)) {
                if (!array_key_exists($key, $value)) {
                    return 0;
                }

                if (true === $exact) {
                    if ($value[$key] == $string) {
                        return 1;
                    }

                    return 0;
                } else {
                    if (str_contains($string, $value[$key])) {
                        return 1;
                        // utmdd([__METHOD__,__LINE__,$name]);
                    }

                    return 0;
                }
            } else {
                if (true === $exact) {
                    if ($value == $string) {
                        return 1;
                    }

                    return 0;
                } else {
                    if (str_contains($value, $string)) {
                        return 1;
                    }

                    return 0;
                }
            }
        });

        return $ret; // [$key[0]];
    }
}
