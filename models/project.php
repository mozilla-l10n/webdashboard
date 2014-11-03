<?php
namespace Webdashboard;

// Check if the locale is working on locamotion
$locamotion = Json::fetch(Utils::cacheUrl(LANG_CHECKER . '?action=listlocales&project=locamotion&json', 15*60));

// Base for the query to get external data
$langchecker_query = LANG_CHECKER . '?locale=all&json';
$locale_done = [];

// include all data about project pages
include __DIR__ . '/../data/project.php';

$project = (array_key_exists($_GET['project'], $pages)) ? $_GET['project'] : 'default';
$pages = $pages[$project];
$sum_pages = count($pages);

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
$total_locales = count(array_unique($locales));

$locales_per_page = [];
$page_descriptions = [];
// Get status from all locales for each page
foreach ($pages as $page) {
    $filename = $page['file'];
    $json_string = $langchecker_query . '&file=' . $filename . '&website=' . $page['site'];
    $data_page = Json::fetch(Utils::cacheUrl($json_string))[$page['file']];
    $locales_per_page[$filename] = array_keys($data_page);
    foreach ($locales as $locale) {
        if (in_array($locale, $locales_per_page[$filename])) {
            $status[$locale][$page['file']] = $data_page[$locale];
            continue;
        }
        $status[$locale][$page['file']] = 'none';
    }
    $page_descriptions[$page['file']] = $page['description'];
}
ksort($status);

// For each locale, for each page, check the status and store it into a new array
$status_formatted = [];
$locale_done_per_page = [];
foreach ($status as $locale => $array_status) {
    $total_page = $page_done = 0;
    foreach ($array_status as $key => $result) {
        // This locale does not have this page
        if ($result != 'none') {
            $total_page++;
            // Page done
            if ($result['Identical'] == 0 && $result['Missing'] == 0) {
                $page_done++;
                $result = 'done';
                (isset($locale_done_per_page[$key]))
                    ? $locale_done_per_page[$key][] = $locale
                    : $locale_done_per_page[$key] = [$locale];
            // Missing
            } elseif ($result['Translated'] == 0) {
                $result = 'missing';

            // In progress
            } else {
                $count = $result['Translated'] + $result['Missing'] + $result['Identical'];
                $result = $result['Translated'] . '/' . $count;
            }
        }
        $status_formatted[$locale][$key] = $result;
    }
    if ($page_done == $total_page) {
        $locale_done[] = $locale;
    }
}
$percent_locale_done = round(count($locale_done) / $total_locales * 100, 2);

// Compute user base coverage for each page then an average
$page_coverage = [];
$sum_percent_covered_users = $sum_locales_per_page = 0;
foreach ($locale_done_per_page as $page => $locales) {
    $page_coverage[$page] = Utils::getUserBaseCoverage($locales);
    $sum_percent_covered_users += $page_coverage[$page];
    $sum_locales_per_page += count($locales);
}

$perfect_locales_coverage = Utils::getUserBaseCoverage($locale_done);
$average_coverage = round($sum_percent_covered_users / $sum_pages, 2);
$average_nb_locales = round($sum_locales_per_page / $sum_pages, 2);


include __DIR__ . '/../views/project.php';
