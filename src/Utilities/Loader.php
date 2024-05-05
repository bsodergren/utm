<?php

namespace UTM\Utilities;

use Camoo\Config\Config;
use Nette\Loaders\RobotLoader;

class Loader extends RobotLoader
{
    public static function loadDatabase($config, $define_pattern, $prefix_pattern, $suffix = '')
    {
        $DbConfig = new Config($config);

        foreach ($DbConfig as $key => $data) {
            $define_name = $prefix_pattern.strtoupper($key.'_prefix').$suffix;
            define($define_name, $data['prefix']);
            foreach ($data['tables'] as $k => $t) {
                $table_def_name = $define_pattern.strtoupper($t).$suffix;
                define($table_def_name, $data['prefix'].$t);
            }
        }
    }
}
