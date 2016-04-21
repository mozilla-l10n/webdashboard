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

$snippets_may16 = [
    [
        'file'        => 'snippets/2016/may2016_a.lang',
        'description' => 'Snippets - May 9',
        'site'        => 6,
    ],
    [
        'file'        => 'snippets/2016/may2016_b.lang',
        'description' => 'Snippets - May 9',
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
    'fall2015' => [
        'pages'   => $fall15,
        'title'   => 'Fall Release 2015 - Nov 3',
        'summary' => true,
    ],
    'snippets_may2016' => [
        'pages'   => $snippets_may16,
        'title'   => 'Snippets May 2016',
        'summary' => false,
    ],
];
