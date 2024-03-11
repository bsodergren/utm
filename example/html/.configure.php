<?php  


use UTM\Template\Template;
use UTM\Utilities\UtmDevice;


define('__PROJECT_ROOT__', dirname(__FILE__, 3));
define('__PUBLIC_ROOT__', dirname(__FILE__, 2));
define('__HTTP_ROOT__', dirname(__FILE__, 1));

define('__COMPOSER_DIR__', __PROJECT_ROOT__.\DIRECTORY_SEPARATOR.'vendor');
require __COMPOSER_DIR__.\DIRECTORY_SEPARATOR.'autoload.php';


define('__PROJECT_SOURCE__', __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'src');
define('__CONFIG_ROOT__', __PROJECT_SOURCE__.\DIRECTORY_SEPARATOR.'Configuration');


define('__ASSETS_ROOT__', __HTTP_ROOT__.\DIRECTORY_SEPARATOR.'assets');
define('__THEME_DIR__', __ASSETS_ROOT__);
define('__TEMPLATE_DIR__', 'templates');


define('__URL_PATH__', '');
define('__URL_HOME__', 'http://'.$_SERVER['HTTP_HOST'].__URL_PATH__);
define('__URL_LAYOUT__', __URL_HOME__.'/assets');

define('__CURRENT_URL__', __URL_HOME__.'/index.php');
define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));

$nav_bar_links = [
    'Home' => '/index.php',
];

define('__NAVBAR_LINKS__', $nav_bar_links);

// require_once __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'composer.php';

// set_include_path(get_include_path().PATH_SEPARATOR.__COMPOSER_DIR__);
require_once __ASSETS_ROOT__.'/includes/navbar.php';