<?php 
use UTM\Utilities\Utilities;
use UTM\Utilities\UtmDevice;
use UTM\Template\Template;


function NavbarDropDown()
{
    foreach (__NAVBAR_LINKS__ as $text => $url) {
        if (\is_array($url)) {
            $dropddown_menu_text = $text;

            foreach ($url as $dropdown_text => $dropdown_url) {
                $dropdown_link_html .= Template::GetHTML(
                    'base/navbar/list/navbar_link',
                    ['DROPDOWN_URL' => $dropdown_url, 'DROPDOWN_URL_TEXT' => $dropdown_text]
                );
            }

            continue;
        }
        $nav_link_html .= Template::GetHTML('base/navbar/navbar_item_link', ['NAV_LINK_URL' => $url, 'NAV_LINK_TEXT' => $text]);
    }

    return [$dropdown_link_html, $nav_link_html, $dropddown_menu_text];
}

function NavbarLatestVersion()
{
    $latest = '1.2';
    $installed = '1.1';
    $dropdown_link_html = Template::GetHTML(
        'base/navbar/list/navbar_item',
        ['DROPDOWN_TEXT' => 'Version '.$installed]
    );

    $latest_version_html = '';
    if (null !== $latest) {
        $dropdown_link_html .= Template::GetHTML(
            'base/navbar/list/navbar_item',
            ['DROPDOWN_TEXT' => 'New! '.$latest]
        );
        //  $latest_version_html = $this->template->template('base/footer/version_latest', ['VERSION' => $latest]);
    }

    return [$dropdown_link_html, $latest_version_html];
}

