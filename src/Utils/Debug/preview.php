<?php
/**
 * Command like Metatag writer for video files.
 */

namespace Mediatag\Utilities\Debug;

use Mediatag\Utilities\Option;

trait preview
{
    public function preview($text, $exit = false)
    {
        if (Option::isTrue('preview')) {
            $this->output->write($text);
            if (true === $exit) {
                exit;
            }
        }
    }
}
