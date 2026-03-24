<?php

/**
 * Command like Metatag writer for video files.
 */

namespace UTM\Bundle\Stash;

use UTM\Bundle\Stash\Drivers\File;
use UTM\Bundle\Stash\Exceptions\InvalidDriverException;
use UTM\Bundle\Stash\Interfaces\Cacheable;

class Cache
{
    /**
     * Instantiate the desired cache driver object.
     *
     * @param  string  $driver  Driver to initialize
     * @param  \Closure  $config  Driver-specific configuration closure
     * @return Cacheable A Cacheable object
     *
     * @throws InvalidDriverException
     */
    public static function make($driver, \Closure $config)
    {
        @trigger_error('The Stash::make() method has been deprecated and will be'
            . ' removed in a future version. Use a specific named-constructor'
            . ' instead.', \E_USER_DEPRECATED);

        if (! method_exists(__CLASS__, $driver)) {
            throw new InvalidDriverException('Unable to initialize driver of type ' . $driver);
        }

        return self::$driver($config);
    }

    /**
     * Instantiate the File cache driver object.
     *
     * @param  \Closure  $config  A configuration closure
     * @return File A cache object
     */
    public static function file(\Closure $config)
    {
        return new File($config);
    }
}
