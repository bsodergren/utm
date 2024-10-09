<?php
/**
 * Command like Metatag writer for video files.
 */

namespace UTM\Utilities;

use Nette\Utils\FileSystem;

class Utilities
{
    public static function isTrue($define_name)
    {
        if (defined($define_name)) {



            if (constant($define_name) == true) {
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

        $string_ret = str_replace(",", "", $string);
        return $string_ret;
    }
    public static function get_filelist($directory, $ext = 'log', $basename = false)
    {
        $files_array = [];

        if (is_dir($directory)) {
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
            foreach ($rii as $file) {
                if ($file->isDir()) {
                    continue;
                }
                $filename = $file->getPathname();
                $filename = FileSystem::normalizePath($filename);
                if (preg_match('/(' . $ext . ')$/', $filename)) {
                    if (true == $basename) {
                        $files_array[] = basename($filename);
                    } else {
                        $files_array[] = $filename;
                    }
                }
            }
        }

        return $files_array;
    }
}
