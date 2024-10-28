<?php
/**
 * UTM Common classes
 */

namespace UTM\Utilities\Debug;

class UTMTimer
{
    private static float $origin;
    private static float $lastTimer;

    public static function start()
    {
        self::$origin = self::getNow();
        return self::$origin;
    }

    /**
     * Return the current time relative to origin in milliseconds.
     */
    protected static function getNow(): float
    {
        self::$lastTimer = self::formatTime(microtime(true) * 1000 - self::$origin);
        return self::$lastTimer;
    }

    /**
     * Formats a time.
     *
     * @throws \InvalidArgumentException When the raw time is not valid
     */
    private static function formatTime(float $time): float
    {
        return round($time, 1);
    }

    /**
     * Gets the duration of the events in milliseconds (including all periods).
     */
    public static function getDuration()
    {
        $lastTime    = self::$lastTimer;
        $currentTime = self::getNow();
        $duration    = self::formatTime($currentTime  - $lastTime);
        utmdump([$duration,$lastTime,$currentTime]);
    }

}
