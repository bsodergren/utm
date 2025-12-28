<?php

declare(strict_types=1);

namespace UTM\Bundle\Tail;

use UTM\Bundle\Tail\Exceptions\FailedToCloseFile;
use UTM\Bundle\Tail\Exceptions\FailedToReadFile;
use UTM\Bundle\Tail\Exceptions\FileClosed;
use UTM\Bundle\Tail\Exceptions\NotAFile;
use UTM\Bundle\Tail\Exceptions\NotSeekable;
use UTM\Utilities\Colors;
use UTM\Utilities\Utilities;

final class Tail
{
    /**
     * @param  string  $absoluteFilePath
     * @param  int  $numberOfLines
     * @return string[]
     *
     * @throws FailedToReadFile
     * @throws FileClosed
     * @throws FailedToCloseFile
     * @throws NotSeekable
     * @throws NotAFile
     */
    public static $lines = [];

    private object $colors;

    public function __construct()
    {
        $this->colors = new Colors;
    }

    public function array()
    {
        return self::$lines;
    }

    public function text()
    {
        return implode("\n", self::$lines);
    }

    public function color()
    {
        foreach (self::$lines as $line) {
            utmdump($line);
        }
    }

    public static function tail(string $absoluteFilePath, int $numberOfLines): Tail
    {
        $lines = [];

        $fileReader       = new FileReader($absoluteFilePath);
        $currentCharacter = $fileReader->readPreviousCharacterSkippingNewLineCharacters();

        $currentLine = '';

        while ($currentCharacter->isPartOfALine() && count($lines) < $numberOfLines) {
            $currentLine      = $currentCharacter->get() . $currentLine;
            $currentCharacter = $fileReader->readPreviousCharacter();

            if ($currentCharacter->isNewLine()) {
                $lines[]          = $currentLine;
                $currentLine      = '';
                $currentCharacter = $fileReader->readPreviousCharacterSkippingNewLineCharacters();
            }
        }

        if (! empty($currentLine)) {
            $lines[] = $currentLine;
        }

        $fileReader->closeFile();

        self::$lines = array_reverse($lines);

        return new self;
    }
}
