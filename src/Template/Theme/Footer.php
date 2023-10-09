<?php
/**
 * Bsodergren\utm Media tool for load flags
 */

namespace UTM\Template\Theme;

use UTM\Template\Template;
use UTM\Template\HTMLDocument;


class Footer extends HTMLDocument
{
    // public $html;

    public static function display($template = '', $params = [])
    {
            $params['FOOT_NAV_PANEL'] = Template::GetHTML(
                'base/footer/settings_nav',
                ['FOOTER_NAV_HTML' => __FOOTER_NAV_HTML__,
                'VERSIONS_HTML' => self::_footerVersionUpdates(),
                ]
            );
        

        $params['END_JAVASCRIPT'] = Template::GetHTML('base/footer/javascript');
        echo Template::GetHTML('base/footer/footer', $params);
    }
}
