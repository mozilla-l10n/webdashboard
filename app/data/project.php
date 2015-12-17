<?php
// This is the list of the tracked pages

$default_page = [
    [
        'file'        => 'main.lang',
        'description' => 'main.lang',
        'site'        => 0,
    ],
];

$fall15 = [
    [
        'file'        => 'firefox/choose.lang',
        'description' => 'Campaign Landing',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/private-browsing.lang',
        'description' => 'Private Browsing',
        'site'        => 0,
    ],
    [
        'file'        => 'mozorg/home/index.lang',
        'description' => 'Home',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/tracking-protection-tour.lang',
        'description' => 'TP UI Tour',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/whatsnew_42.lang',
        'description' => 'Whatsnew and Firstrun',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/android/index.lang',
        'description' => 'Android',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/desktop/trust.lang',
        'description' => 'Trust Page',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/push.lang',
        'description' => 'Push',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/family/index.lang',
        'description' => 'Family Page',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/ios.lang',
        'description' => 'iOS',
        'site'        => 0,
    ],
    [
        'file'        => 'firefox/new.lang',
        'description' => 'Download (/new)',
        'site'        => 0,
    ],
    [
        'file'        => 'main.lang',
        'description' => 'Main (Shared)',
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

$snippets_fall15 = [
    [
        'file'        => 'snippets/fall2015.lang',
        'description' => 'Snippets Fall Campaign - Nov 2',
        'site'        => 6,
    ],
    [
        'file'        => 'snippets/nov2015_eoy_mofo.lang',
        'description' => 'Snippets EOY - Nov 2',
        'site'        => 6,
    ],
    [
        'file'        => 'tiles/tiles_nov2015.lang',
        'description' => 'Tiles - Nov 2',
        'site'        => 6,
    ],
];

$snippets_nov15 = [
    [
        'file'        => 'snippets/nov2015_a.lang',
        'description' => 'Snippets A - Nov 14',
        'site'        => 6,
    ],
    [
        'file'        => 'snippets/nov2015_b.lang',
        'description' => 'Snippets B - Nov 14',
        'site'        => 6,
    ],
];

$snippets_dec15 = [
    [
        'file'        => 'snippets/dec2015_a.lang',
        'description' => 'Snippets A - Dec 14',
        'site'        => 6,
    ],
    [
        'file'        => 'snippets/dec2015_b.lang',
        'description' => 'Snippets B - Dec 14',
        'site'        => 6,
    ],
];

$snippets_jan16 = [
    [
        'file'        => 'snippets/jan2016.lang',
        'description' => 'Snippets - Jan 15',
        'site'        => 6,
    ],
    [
        'file'        => 'tiles/tiles_jan2016.lang',
        'description' => 'Tiles - Jan 15',
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
    'fall2015' => [
        'pages'   => $fall15,
        'title'   => 'Fall Release 2015 - Nov 3',
        'summary' => true,
    ],
    'snippets_fall2015' => [
        'pages'   => $snippets_fall15,
        'title'   => 'Snippets Fall Campaign 2015',
        'summary' => false,
    ],
    'snippets_nov2015' => [
        'pages'   => $snippets_nov15,
        'title'   => 'Snippets November 2015',
        'summary' => false,
    ],
    'snippets_dec2015' => [
        'pages'   => $snippets_dec15,
        'title'   => 'Snippets December 2015',
        'summary' => false,
    ],
    'snippets_jan2016' => [
        'pages'   => $snippets_jan16,
        'title'   => 'Snippets January 2016',
        'summary' => false,
    ],
];
