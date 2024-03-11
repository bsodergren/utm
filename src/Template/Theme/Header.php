<?php

namespace UTM\Template\Theme;
use UTM\Utilities\UtmDevice;
use UTM\Template\Template;
use UTM\Template\HTMLDocument;


class Header extends HTMLDocument
{
    public static function display($template = '', $params = [])
    {
        [$js,$onload] = self::header_JS();

        \define('__CUSTOM_JS__', $js);
        \define('__ONLOAD__', $onload);

        $params['FAV_ICON'] = UtmDevice::getAssetURL('image', ['/images/favicon.png']);
        $params['CSS_SRC'] = UtmDevice::getAssetURL('css', ['css/app.css', 'css/custom.css']);
        $params['JS_SRC'] = UtmDevice::getAssetURL('js', ['js/app.js', 'js/jquery-3.4.1.min.js']);

        [$params['BOOTSTRAP'] ,$params['DEFAULT_CSS']] = self::header_CSS();

        $params['__NAVBAR__'] = self::_getNavbar();

        $params['__MSG__'] = self::displayMsg();

        echo Template::GetHTML('base/header/header', $params);
    }
}
