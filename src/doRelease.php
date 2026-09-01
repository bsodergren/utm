<?php

namespace UTM;

use Nette\Utils\FileSystem;

class doRelease
{
    public static $versionFile = 'current';

    public static function execute()
    {
        $file  = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR.self::$versionFile;
        $lines = FileSystem::readLines($file);

foreach ($lines as $lineNum => $line) {
	echo "Line $lineNum: $line\n";
}    }
}
