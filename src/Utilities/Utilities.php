<?php

/**
 * UTM Common classes
 */

namespace UTM\Utilities;

use DirectoryIterator;
use Nette\Utils\FileSystem;
use RuntimeException;
use UnexpectedValueException;

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
        $string_ret = str_replace(',', '', $string);

        return $string_ret;
    }

    public static function get_filelist($path, $ext = 'log', $basename = false, $daysOld = null)
    {
        $directoryPath = $path; // Change to your target directory

        $files_array = [];
        try {
            // Validate directory
            if (! is_dir($directoryPath)) {
                throw new RuntimeException("Directory does not exist: $directoryPath");
            }

            $now = time();
            if (! is_null($daysOld)) {
                $cutoff = $now - ($daysOld * 24 * 60 * 60); // Convert days to seconds
            }

            $dir = new DirectoryIterator($directoryPath);

            foreach ($dir as $fileInfo) {
                // Skip . and ..
                if ($fileInfo->isDot()) {
                    continue;
                }

                // Only process files (skip subdirectories)
                if ($fileInfo->isFile()) {
                    $filePath  = $fileInfo->getPathname();
                    $fileMTime = $fileInfo->getMTime();
                    if (strpos($fileInfo->getFilename(), $ext) > 0) {
                        if (! is_null($daysOld)) {
                            if ($fileMTime > $cutoff) {
                                continue;
                            }
                        }
                        $files_array[] = $filePath;
                    }
                }
            }
        } catch (UnexpectedValueException $e) {
            echo 'Error opening directory: ' . $e->getMessage() . "\n";
        } catch (RuntimeException $e) {
            echo 'Runtime error: ' . $e->getMessage() . "\n";
        }

        return $files_array;

        // $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::FOLLOW_SYMLINKS);
        // $filter    = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) use ($ext) {
        //     // Skip hidden files and directories.
        //     if ($current->getFilename()[0] === '.') {
        //         return false;
        //     }
        //     if ($current->isDir()) {
        //         // Only recurse into intended subdirectories.
        //         return $current->getFilename() === 'wanted_dirname';
        //     } else {
        //         // Only consume files of interest.
        //         utmdump(date('g:i a', $current->getMTime()));

        //         return strpos($current->getFilename(), $ext) > 0;
        //     }
        // });
        // $iterator    = new \RecursiveIteratorIterator($filter);
        // $files_array = [];
        // foreach ($iterator as $info) {
        //     $files_array[] = $info->getPathname();
        // }
        // $files_array = [];

        // if (is_dir($directory)) {
        //     $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        //     foreach ($rii as $file) {
        //         if ($file->isDir()) {
        //             continue;
        //         }
        //         $filename = $file->getPathname();
        //         $filename = FileSystem::normalizePath($filename);
        //         if (preg_match('/(' . $ext . ')$/', $filename)) {
        //             if ($basename == true) {
        //                 $files_array[] = basename($filename);
        //             } else {
        //                 $files_array[] = $filename;
        //             }
        //         }
        //     }
        // }

        return $files_array;
    }
}
