<?php
/**
 * UTM Common classes
 */

namespace UTM\Utilities;

use Nette\Utils\FileSystem;

class File
{
    public static function replace($file, $search, $replacement)
    {
        $file    = FileSystem::platformSlashes($file);

        $lines   = FileSystem::readLines($file);
        foreach ($lines as $lineNum => $line) {
            $text[] = str_replace($search, $replacement, $line);
        }
        $content = implode("\n", $text);
        FileSystem::write($file, $content);
    }
}
