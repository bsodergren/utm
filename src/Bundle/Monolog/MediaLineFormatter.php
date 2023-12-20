<?php
/**
 * Command like Metatag writer for video files.
 */

declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UTM\Bundle\Monolog;

use Monolog\Formatter\LineFormatter;
use Monolog\Utils;

/**
 * Formats incoming records into a one-line string.
 *
 * This is especially useful for logging to files
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Christophe Coevoet <stof@notk.org>
 */
class MediaLineFormatter extends LineFormatter
{
    public bool $allowInlineLineBreaks;

    public function stringify($value): string
    {
        if (\is_array($value)) {
            if (0 == \count($value)) {
                return '';
            }
        }

        return trim($this->replaceNewlines($this->convertToString($value)), '[,",]');
    }

    protected function replaceNewlines(string $str): string
    {
        if ($this->allowInlineLineBreaks) {
            $str = preg_replace('/(?<!\\\\)\\\\[rn]/', "\n", $str);
            if (null === $str) {
                $pcreErrorCode = preg_last_error();
                throw new \RuntimeException('Failed to run preg_replace: '.$pcreErrorCode.' / '.Utils::pcreLastErrorMessage($pcreErrorCode));
            }

            return $str;
        }

        return str_replace(['\\r', '\\n'], '', $str);
    }
}
