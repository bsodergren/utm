<?php
/**
 * UTM Common classes
 */

use Symfony\Component\Stopwatch\Stopwatch;
use UTM\Utilities\Debug\Timer;

/**
 * Command like Metatag writer for video files.
 */


if (!function_exists('TimerStart')) {
    function TimerStart()
    {
        Timer::$Obj = new Timer(new Stopwatch());
        Timer::$Obj->start();
    }


}
if (!function_exists('TimerNow')) {
    function TimerNow()
    {
        return Timer::$Obj->watch();
    }


}
