<?php
// This is the list of the tracked pages

$default_page = [
    [
        'file'        => 'main.lang',
        'description' => 'main.lang',
        'site'        => 0,
    ],
];

$fx10 = [
    [
        'file'        => 'firefox/privacy_tour/privacy_tour.lang',
        'description' => 'Privacy Tour',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/independent.lang',
        'description' => 'Anniversary Landing',
        'site'        => 0,
    ],
    [
        'file'        => 'mozorg/home/index.lang',
        'description' => 'Home',
        'site'        => 0,
    ],
    [
        'file'        => 'main.lang',
        'description' => 'Main (shared)',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/tiles.lang',
        'description' => 'Tiles Landing',
        'site'        => 0,
    ],
    [
        'file'        => 'tiles.lang',
        'description' => 'Tiles',
        'site'        => 10,
    ],
    [
        'file'        => 'firefox/new.lang',
        'description' => 'Download Page',
        'site'        => 0,
    ],
    [
        'file'        => 'privacycoach.lang',
        'description' => 'Privacy&nbsp;Coach Add-on',
        'site'        => 7,
    ],
    [
        'file'        => 'description_page.lang',
        'description' => 'Google Play',
        'site'        => 12,
    ],
    [
        'file'        => 'nov2014_a.lang',
        'description' => 'Snippets A',
        'site'        => 6,
    ],
    [
        'file'        => 'nov2014_b.lang',
        'description' => 'Snippets B',
        'site'        => 6,
    ],
    [
        'file'        => 'nov2014_c.lang',
        'description' => 'Snippets C',
        'site'        => 6,
    ],
    [
        'file'        => 'nov2014_d.lang',
        'description' => 'Snippets D',
        'site'        => 6,
    ],
    [
        'file'        => 'nov2014_e.lang',
        'description' => 'Snippets E',
        'site'        => 6,
    ],
];

$fx10_extra = [
    [
        'file'        => 'firefox/developer.lang',
        'description' => 'Developer Edition',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/channel.lang',
        'description' => 'Channel',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/desktop/trust.lang',
        'description' => 'Trust Page',
        'site'        => 0,
    ],
];

$firefox_os = [
    [
        'file'        => 'firefox/os/index.lang',
        'description' => 'Firefox OS Index',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/os/devices.lang',
        'description' => 'Firefox OS Devices',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/os/faq.lang',
        'description' => 'Firefox OS FAQ',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/partners/index.lang',
        'description' => 'Firefox OS Partners',
        'site'        => 0,
    ],
];

$firefox_os_extra = [
    [
        'file'        => 'marketplace_l10n_feed.lang',
        'description' => 'Marketplace Feed',
        'site'        => 9,
    ],
    [
        'file'        => 'screenshots.lang',
        'description' => 'Screenshots 1.1',
        'site'        => 9,
    ],
    [
        'file'        => 'screenshots_2_0.lang',
        'description' => 'Screenshots 2.0',
        'site'        => 9,
    ],
    [
        'file'        => 'screenshots_tarako.lang',
        'description' => 'Screenshots Tarako',
        'site'        => 9,
    ],
    [
        'file'        => 'screenshots_dolphin.lang',
        'description' => 'Screenshots Dolphin',
        'site'        => 9,
    ],
];

$snippets_mar15 = [
    [
        'file'        => 'mar2015_a.lang',
        'description' => 'Snippets A - Mar 2',
        'site'        => 6,
    ],
    [
        'file'        => 'mar2015_b.lang',
        'description' => 'Snippets B - Mar 2',
        'site'        => 6,
    ],
];

$snippets_apr15 = [
    [
        'file'        => 'apr2015.lang',
        'description' => 'Snippets Content - Apr 2',
        'site'        => 6,
    ],
];

$snippets_may15 = [
    [
        'file'        => 'may2015_a.lang',
        'description' => 'Snippets Content A - May 11',
        'site'        => 6,
    ],
    [
        'file'        => 'may2015_b.lang',
        'description' => 'Snippets Content B - May 11',
        'site'        => 6,
    ],
    [
        'file'        => 'spring2015.lang',
        'description' => 'Snippets Spring Release - May 25',
        'site'        => 6,
    ],
];

/*
* Each project has a key name that will be used in the URL.
* Value is an array with:
* - pages: list of files
* - title: text to be displayed in the project page as title
* - summary: if we need to display the summary table with good/bad locales
*/

$projects = [
    'default' => [
        'pages'   => $default_page,
        'title'   => 'Default',
        'summary' => true,
    ],
    'firefox_os' => [
        'pages'   => $firefox_os,
        'title'   => 'Firefox OS',
        'summary' => true,
    ],
    'firefox_os_all' => [
        'pages'   => array_merge($firefox_os, $firefox_os_extra),
        'title'   => 'Firefox OS Complete',
        'summary' => true,
    ],
    'fx10' => [
        'pages'   => $fx10,
        'title'   => 'Firefox 10th Anniversary',
        'summary' => true,
    ],
    'fx10_all' => [
        'pages'   => array_merge($fx10, $fx10_extra),
        'title'   => 'Firefox 10th Anniversary Complete',
        'summary' => true,
    ],
    'snippets_mar2015' => [
        'pages'   => $snippets_mar15,
        'title'   => 'Snippets March 2015',
        'summary' => false,
    ],
    'snippets_apr2015' => [
        'pages'   => $snippets_apr15,
        'title'   => 'Snippets April 2015',
        'summary' => false,
    ],
    'snippets_may2015' => [
        'pages'   => $snippets_may15,
        'title'   => 'Snippets May 2015',
        'summary' => false,
    ],
];
