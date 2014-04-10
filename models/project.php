<?php
namespace Webdashboard;
// Fetch external data source
$langchecker        = 'http://l10n.mozilla-community.org/~pascalc/langchecker/';
$langchecker_query = $langchecker . '?locale=all&json';
$locale_done = 0;

// include all data about project pages
include __DIR__ . '/../data/project.php';


$project = (array_key_exists($_GET['project'], $pages)) ? $_GET['project'] : 'default';
$pages = $pages[$project];

// Get all locales from project pages list
$locales = [];
foreach ($pages as $page) {
    $json_string = $langchecker_query . '&file=' . $page['file'] . '&website=' . $page['site'];
    $data_page = Json::fetch(Utils::cacheUrl($json_string))[$page['file']];
    foreach ($data_page as $key => $val) {
        if (in_array($key, $locales)) {
            continue;
        }
        $locales[] = $key;
    }
}

// Get status from all locales for each page
foreach ($pages as $page) {
    $json_string = $langchecker_query . '&file=' . $page['file'] . '&website=' . $page['site'];
    $data_page = Json::fetch(Utils::cacheUrl($json_string))[$page['file']];
    foreach ($locales as $locale) {
        if (in_array($locale, array_keys($data_page))) {
            $status[$locale][$page['file']] = $data_page[$locale];
            continue;
        }
        $status[$locale][$page['file']] = 'none';
    }
}

ksort($status);
$total_locales = count(array_unique($locales));

include __DIR__ . '/../views/project.php';
