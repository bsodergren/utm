<?php
/**
 * Bsodergren\utm Media tool for load flags
 */

namespace UTM\Template\Theme;

use UTM\Template\Template;
use UTM\Template\HTMLDocument;


class Navbar extends HTMLDocument
{
    public static function display($template = '', $params = [])
    {
        $doc = new HTMLDocument();

        [$dropdown_link_html,$nav_link_html,$dropddown_menu_text] = NavbarDropDown();

        \define('__FOOTER_NAV_HTML__', $dropdown_link_html);

        [$dropdown_latest,$latest_version_html] = NavbarLatestVersion();

        $dropdown_link_html .= $dropdown_latest;

        $navbar_right_dropdown = Template::GetHTML('base/navbar/'.$doc->nav_list_dir.'/navbar_dropdown', [
                    'DROPDOWN_LINKS' => $dropdown_link_html,
                    'DROPDOWN_TEXT' => $dropddown_menu_text]);

        $params['NAVBAR_LEFT_HTML'] = Template::GetHTML('base/navbar/navbar_left', []);
        $params['NAVBAR_CENTER_HTML'] = Template::GetHTML(
            'base/navbar/navbar_center',
            ['NAVBAR_CENTER_LIST' => $latest_version_html]
        );
        $params['NAVBAR_RIGHT_HTML'] = Template::GetHTML('base/navbar/navbar_right', [
            'NAVBAR_RIGHT_LIST' => $nav_link_html,
            'NAVBAR_RIGHT_DROPDOWN' => $navbar_right_dropdown,
        ]);

        return Template::GetHTML('base/navbar/navbar', $params);
    }
}
