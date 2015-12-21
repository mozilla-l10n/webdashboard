<?php
namespace Webdashboard;

use Cache\Cache;

// Check if the locale is working on locamotion
$json_data = new Json;

$cache_id = 'locamotion_locales';
if (! $locamotion = Cache::getKey($cache_id)) {
    $locamotion = $json_data
        ->setURI(LANG_CHECKER . '?action=listlocales&project=locamotion&json')
        ->fetchContent();
    Cache::setKey($cache_id, $locamotion);
}

// Base for the query to get external data
$langchecker_query = LANG_CHECKER . '?locale=all&json';
$locale_done = [];

// Include all data about project pages
include __DIR__ . '/../data/project.php';

// Fall back to default if the project is not available
$requested_project = array_key_exists($project, $projects) ? $project : 'default';
$project_data = $projects[$requested_project];
$pages = $project_data['pages'];
$sum_pages = count($pages);

// Get all locales from project pages list
$locales = [];
foreach ($pages as $page) {
    $filename = $page['file'];
    $json_string = $langchecker_query . '&file=' . $filename . '&website=' . $page['site'];

    $cache_id = 'page_' . $filename . '_' . $page['site'];
    if (! $data_page = Cache::getKey($cache_id)) {
        $data_page = $json_data
            ->setURI($json_string)
            ->fetchContent()[$filename];
        Cache::setKey($cache_id, $data_page);
    }

    $locales = array_merge($locales, array_keys($data_page));
}

$locales = array_unique($locales);
sort($locales);

$total_locales = count(array_unique($locales));

$locales_per_page = [];
$page_descriptions = [];
// Get status from all locales for each page
foreach ($pages as $page) {
    $filename = $page['file'];
    $json_string = $langchecker_query . '&file=' . $filename . '&website=' . $page['site'];

    $cache_id = 'page_' . $filename . '_' . $page['site'];
    if (! $data_page = Cache::getKey($cache_id)) {
        $data_page = $json_data
            ->setURI($json_string)
            ->fetchContent()[$filename];
        Cache::setKey($cache_id, $data_page);
    }

    $locales_per_page[$filename] = array_keys($data_page);
    foreach ($locales as $locale) {
        if (in_array($locale, $locales_per_page[$filename])) {
            $status[$locale][$filename] = $data_page[$locale];
            continue;
        }
        $status[$locale][$filename] = 'none';
    }
    $page_descriptions[$filename] = $page['description'];
}
ksort($status);

// For each locale and each page, check the status and store it into a new array
$status_formatted = [];
$locale_done_per_page = [];
$stats = [];
foreach ($status as $locale => $array_status) {
    $total_page = $page_done = 0;
    $stats[$locale] = [
        'strings_total' => 0,
        'strings_done'  => 0,
        'complete'      => false,
        'percentage'    => 0,
        'locamotion'    => in_array($locale, $locamotion),
    ];
    foreach ($array_status as $key => $result) {
        $result_message = '';
        $result_status = $result;
        if ($result != 'none') {
            $total_page++;
            // Update stats for this locale
            $stats[$locale]['strings_total'] += $result['Translated'] + $result['Missing'] + $result['Identical'];
            $stats[$locale]['strings_done'] += $result['Translated'];
            if ($result['Identical'] == 0 && $result['Missing'] == 0 && $result['Errors'] == 0) {
                // Page done
                $page_done++;
                $result_status = 'done';
                (isset($locale_done_per_page[$key]))
                    ? $locale_done_per_page[$key][] = $locale
                    : $locale_done_per_page[$key] = [$locale];
            } elseif ($result['Translated'] == 0) {
                // Missing
                $result_status = 'missing';
            } else {
                // In progress
                $count = $result['Translated'] + $result['Missing'] + $result['Identical'];
                $result_message = "{$result['Translated']}/{$count}";
                if ($result['Errors'] > 0) {
                    $result_status = 'errors';
                    $result_message .= " ({$result['Errors']})";
                }
            }
        }
        $status_formatted[$locale][$key] = [
            'message' => $result_message,
            'status'  => $result_status,
        ];
    }

    if ($page_done == $total_page) {
        // Locale is complete
        $locale_done[] = $locale;
        $stats[$locale]['complete'] = true;
        $stats[$locale]['percentage'] = 100;
    } else {
        $stats[$locale]['percentage'] = round($stats[$locale]['strings_done'] / $stats[$locale]['strings_total'] * 100, 2);
    }
}
$percent_locale_done = round(count($locale_done) / $total_locales * 100, 2);

// Compute user base coverage for each page then an average
$page_coverage = [];
$sum_percent_covered_users = $sum_locales_per_page = 0;
foreach ($locale_done_per_page as $page => $locales) {
    $page_coverage[$page] = Utils::getUserBaseCoverage($locales, LANG_CHECKER);
    $sum_percent_covered_users += $page_coverage[$page];
    $sum_locales_per_page += count($locales);
}

$perfect_locales_coverage = Utils::getUserBaseCoverage($locale_done, LANG_CHECKER);
$average_coverage = round($sum_percent_covered_users / $sum_pages, 2);
$average_nb_locales = round($sum_locales_per_page / $sum_pages, 2);

// Organize data in $stats to display a summary of all locales
$locales_summary = [
    'perfect' => [
        'title'              => 'Perfect',
        'description'        => 'No missing strings',
        'locales'            => [],
        'locamotion_locales' => [],
    ],
    'good'    => [
        'title'              => 'Good',
        'description'        => 'Less than 10% missing',
        'locales'            => [],
        'locamotion_locales' => [],
    ],
    'average' => [
        'title'              => 'Average',
        'description'        => 'Between 10% and 40% missing',
        'locales'            => [],
        'locamotion_locales' => [],
    ],
    'bad'     => [
        'title'              => 'Bad',
        'description'        => 'Between 40% and 70% missing',
        'locales'            => [],
        'locamotion_locales' => [],
    ],
    'verybad' => [
        'title'              => 'Very Bad',
        'description'        => 'Over 70% missing',
        'locales'            => [],
        'locamotion_locales' => [],
    ],
];

foreach ($stats as $locale => $locale_stats) {
    $category = '';
    if ($locale_stats['percentage'] == 100) {
        $category = 'perfect';
    } elseif ($locale_stats['percentage'] >= 90) {
        $category = 'good';
    } elseif ($locale_stats['percentage'] >= 60) {
        $category = 'average';
    } elseif ($locale_stats['percentage'] >= 30) {
        $category = 'bad';
    } else {
        $category = 'verybad';
    }

    $locales_summary[$category]['locales'][] = $locale;
    if ($locale_stats['locamotion']) {
        $locales_summary[$category]['locamotion_locales'][] = $locale;
    }
}

include __DIR__ . '/../views/project.php';
