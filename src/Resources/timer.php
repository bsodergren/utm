<?php
use UTM\Utilities\Debug\UTMTimer;
/**
 * Command like Metatag writer for video files.
 */


if (!function_exists('TimerStart')) {
      function TimerStart()
    {
        return UTMTimer::start();
    }


}
if (!function_exists('TimerNow')) {
      function TimerNow()
    {
        return UTMTimer::getNow();
    }

    
}
if (!function_exists('TimerDuration')) {
    function TimerDuration()
  {
      return UTMTimer::getDuration();
  }

  
}
