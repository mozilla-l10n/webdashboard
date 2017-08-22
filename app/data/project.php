<?php
// This is the list of the tracked pages

$default_page = [
    [
        'file'        => 'main.lang',
        'description' => 'main.lang',
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
    'snippets_may2016' => [
        'pages'   => $snippets_may16,
        'title'   => 'Snippets May 2016',
        'summary' => false,
    ],
];
