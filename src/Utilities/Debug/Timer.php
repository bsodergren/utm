<?php
/**
 * Command like Metatag writer for video files.
 */

namespace UTM\Utilities\Debug;

use Symfony\Component\Stopwatch\Stopwatch;

class Timer
{
    public static $Obj;
    public static $logProc = false;

    public function __construct(
        private Stopwatch $stopwatch
    ) {
        $this->stopwatch = $stopwatch;
    }

    public function start()
    {
        $this->stopwatch->start('export-data');
    }

    public function watch()
    {
        // ...
        return (string) $this->stopwatch->getEvent('export-data'); // dumps e.g. '4.50 MiB - 26 ms'
    }
}
