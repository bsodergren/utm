<?php

namespace UTM\Utilities;

use Nette\Utils\Callback;
use Nette\Utils\FileSystem as NetteFile;
use Nette\Utils\Strings;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem as SFilesystem;
use Symfony\Component\Finder\Finder as SFinder;
use Symfony\Component\Process\Process as ExecProcess;

class Filesystem
{
    private static $bckDir = '.bak';

    public static $tempDir = '';

    /**
     * writeFile.
     */
    public static function writeFile($file, $content, $backup = true)
    {
        // utminfo(func_get_args());S

        if (! file_exists($file)) {
            touch($file);
        }

        if (is_array($content)) {
            $content_string = implode("\n", $content);
        } else {
            $content_string = $content;
        }
        if ($backup === true) {
            self::backupFile($file, move: true);
        }
        // $content_string .= PHP_EOL; //. '#  file'.PHP_EOL;
        $out = file_put_contents($file, $content_string . PHP_EOL);
    }

    public static function backupFile($filename, $directory = false, $move = false)
    {
        // utminfo(func_get_args());

        if (! file_exists($filename)) {
            return 0;
        }

        $filesystem    = new SFilesystem;
        $file          = realpath($filename);
        $filename      = basename($file);
        $fileNameNoExt = Strings::before($filename, '.', 1);
        if ($directory === false) {
            $directory = dirname($file);
            $directory = $directory . '/' . self::$bckDir . '/' . $fileNameNoExt;
        }

        if (! is_dir($directory)) {
            $filesystem->mkdir($directory);
        }

        $backupFile = $directory . '/' . $filename;

        $ext = '';

        if (Strings::after($backupFile, '.', -1) == 'old') {
            $fileNo = 1;
        }
        if (is_numeric(Strings::after($backupFile, '.', -1))) {
            $number = Strings::after($backupFile, '.', -1);
            $fileNo = $number + 1;
        }

        if (isset($fileNo)) {
            $ext = '.' . $fileNo;
        }

        $backup_name1 = $fileNameNoExt . '.old' . $ext;

        $backup_name = $directory . '/' . basename($backup_name1);

        if (file_exists($backup_name)) {
            self::backupFile($backup_name, $directory, $move);
        }

        if ($move == true) {
            (new SFilesystem)->rename($file, $backup_name);
        } else {
            (new SFilesystem)->copy($file, $backup_name);
        }

        // return $backup_name;
    }
}
