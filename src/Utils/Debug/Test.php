<?php
/**
 * Command like Metatag writer for video files.
 */

namespace Mediatag\Utilities\Debug;

use Mediatag\Utilities\Option;

trait Test
{
    public function test($text, $exit = false)
    {
        if (Option::isTrue('test')) {
            $this->output->write($text);
            if (true === $exit) {
                exit;
            }
        }
    }
}
