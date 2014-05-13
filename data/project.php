<?php
// This is the list of the tracked pages


$australis_core = [
        ['file' => 'firefox/desktop/index.lang',
         'site' => 0],
        ['file' => 'firefox/desktop/fast.lang',
         'site' => 0],
        ['file' => 'firefox/desktop/trust.lang',
         'site' => 0],
        ['file' => 'firefox/desktop/customize.lang',
         'site' => 0],
        ['file' => 'firefox/australis/firefox_tour.lang',
         'site' => 0],
        ['file' => 'firefox/sync.lang',
         'site' => 0]
    ];

$australis_mozorg = $australis_core;
$australis_mozorg[] = ['file' => 'firefox/new.lang','site' => 0];
$australis_mozorg[] = ['file' => 'mozorg/home.lang', 'site' => 0];
$australis_mozorg[] = ['file' => 'tabzilla/tabzilla.lang', 'site' => 0];

$australis_all = $australis_mozorg;
$australis_all[] = ['file' => 'main.lang', 'site' => 0];
$australis_all[] = ['file' => 'apr2014.lang', 'site' => 6];

$pages = [
    'default' => [
        ['file' => 'main.lang',
         'site' => 0],
    ],
    'australis_core' => $australis_core,
    'australis_mozorg' => $australis_mozorg,
    'australis_all' => $australis_all,
    'firefox_os' => [
        ['file' => 'firefox/os/index.lang',
         'site' => 0],
        ['file' => 'firefox/os/faq.lang',
         'site' => 0],
        ['file' => 'firefox/partners/index.lang',
         'site' => 0],
    'firefox_usage' => [
        ['file' => 'firefox/desktop/tips.lang',
         'site' => 0],
    ],
    ],
];
