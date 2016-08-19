<?php
namespace Webdashboard;

use Cache\Cache;

// Get the list of locales working on Locamotion
$cache_id = 'locamotion_locales';
if (! $locamotion = Cache::getKey($cache_id)) {
    $locamotion = $json_object
        ->setURI(LANG_CHECKER . '?action=listlocales&project=locamotion&json')
        ->fetchContent();
    Cache::setKey($cache_id, $locamotion);
}

// Base for the query to get external data
$langchecker_query = LANG_CHECKER . '?locale=all&json';
$locales_done = [];

// Include all data about project pages
include __DIR__ . '/../data/project.php';

// Fall back to default if the project is not available
$requested_project = array_key_exists($project, $projects) ? $project : 'default';
$project_data = $projects[$requested_project];
$pages = $project_data['pages'];
$total_pages = count($pages);

/*
    Analyze all pages and create:
    - An array ($project_locales) with the list of all supported locales for
      this project.
    - An array ($project_pages) with information on each page: description,
      supported locales, website.
    - An array ($status) with data for all pages in a locale.
*/
$project_locales = [];
$project_pages = [];
$status = [];

foreach ($pages as $page) {
    $filename = $page['file'];
    $website = $page['site'];
    $json_query_string = "{$langchecker_query}&file={$filename}&website={$website}";

    $cache_id = "page_{$filename}_{$website}";
    if (! $page_data = Cache::getKey($cache_id)) {
        $page_data = $json_object
            ->setURI($json_query_string)
            ->fetchContent()[$filename];
        Cache::setKey($cache_id, $page_data);
    }

    $page_locales = array_keys($page_data);
    $project_pages[$filename] = [
        'complete_locales'  => [],
        'description'       => $page['description'],
        'percentage'        => 0,
        'supported_locales' => $page_locales,
        'website'           => $website,
    ];

    // Update list of locale supported for the project
    $project_locales = array_merge($project_locales, $page_locales);

    foreach ($page_locales as $locale) {
        $locale_data = $page_data[$locale];
        // If it's the first time I analyze this locale, initialize general values
        if (! isset($status[$locale])) {
            $status[$locale]['general'] = [
                'complete_pages' => 0,
                'complete'       => false,
                'done_strings'   => 0,
                'locamotion'     => in_array($locale, $locamotion),
                'total_pages'    => 0,
                'total_strings'  => 0,
            ];
        }

        // Add this page to the overall count for this locale
        $status[$locale]['general']['total_pages'] += 1;

        $result_message = '';
        $result_status = '';
        if ($locale_data['Identical'] == 0 && $locale_data['Missing'] == 0 && $locale_data['Errors'] == 0) {
            // File is completely localized
            $result_status = 'done';
            $project_pages[$filename]['complete_locales'][] = $locale;
            $status[$locale]['general']['complete_pages'] += 1;
        } elseif ($locale_data['Translated'] == 0) {
            $result_status = 'missing';
        } else {
            $count = $locale_data['Translated'] + $locale_data['Missing'] + $locale_data['Identical'];
            $result_message = "{$locale_data['Translated']}/{$count}";
            if ($locale_data['Errors'] > 0) {
                $result_status = 'errors';
                $result_message .= " ({$locale_data['Errors']})";
            }
        }

        $done_strings = $locale_data['Translated'];
        $total_strings = $done_strings + $locale_data['Missing'] + $locale_data['Identical'];

        // Store stats for this locale+page
        $status[$locale][$filename] = [
            'done_strings'  => $done_strings,
            'message'       => $result_message,
            'status'        => $result_status,
            'total_strings' => $total_strings,
        ];

        // Update general stats for this locale
        $status[$locale]['general']['total_strings'] += $total_strings;
        $status[$locale]['general']['done_strings'] += $done_strings;
    }
}
$project_locales = array_unique($project_locales);
sort($project_locales);
ksort($status);

// Organize data in $stats to display a summary of all locales
$locales_summary = [
    'perfect' => [
        'title'              => 'Perfect',
        'description'        => 'No missing strings',
        'locales'            => [],
        'locamotion_locales' => [],
    ],
    'good' => [
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
    'bad' => [
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

// Calculate stats for each locale and put it in a group
$complete_locales = [];
foreach ($status as $locale => $locale_status) {
    $stats = $locale_status['general'];
    if ($stats['total_pages'] == $stats['complete_pages']) {
        $status[$locale]['general']['complete'] = true;
        $status[$locale]['general']['percentage'] = 100;
        $locales_summary['perfect']['locales'][] = $locale;
        if ($stats['locamotion']) {
            $locales_summary['perfect']['locamotion_locales'][] = $locale;
        }
        $complete_locales[] = $locale;
    } else {
        $percentage = round($stats['done_strings'] / $stats['total_strings'] * 100, 2);
        $status[$locale]['general']['percentage'] = $percentage;
        if ($percentage >= 90) {
            $category = 'good';
        } elseif ($percentage >= 60) {
            $category = 'average';
        } elseif ($percentage >= 30) {
            $category = 'bad';
        } else {
            $category = 'verybad';
        }
        $locales_summary[$category]['locales'][] = $locale;
        if ($stats['locamotion']) {
            $locales_summary[$category]['locamotion_locales'][] = $locale;
        }
    }
}

// Calculate coverage for each page
$sum_locales_per_page = 0;
$sum_percent_covered_users = 0;
foreach ($project_pages as $filename => $project) {
    $coverage = Utils::getUserBaseCoverage($project['complete_locales'], LANG_CHECKER);
    $project_pages[$filename]['coverage'] = $coverage;
    $sum_locales_per_page += count($project['complete_locales']);
    $sum_percent_covered_users += $coverage;
}

// Create data for main table
$maintable_rows = [];
foreach ($status as $locale => $locale_status) {
    $working_on_locamotion = in_array($locale, $locamotion);
    // Add extra class if locale is complete
    $row_class = $locale_status['general']['complete'] ? 'complete_locale' : '';
    if ($working_on_locamotion) {
        $row_class .= ' locamotion_locale';
    }

    $missing_strings = $locale_status['general']['total_strings'] - $locale_status['general']['done_strings'];
    if ($missing_strings == 0) {
        $missing_strings = '';
    }

    $cells = [];
    foreach ($locale_status as $page => $result) {
        // Ignore 'general'
        if ($page == 'general') {
            continue;
        }

        $cell = $class = '';
        switch ($result['status']) {
            case 'done':
                // Page done
                $cell = '100%';
                $class = 'project_done';
                break;
            case 'errors':
                // Errors
                $cell = $result['message'];
                $class = 'project_with_errors';
                break;
            case 'missing':
                // Missing strings
                $cell = '0%';
                $class = 'project_missing';
                break;
            default:
                // In progress
                $cell = $result['message'];
                $class = 'project_in_progress';
                break;
        }
        $cells[$page] = [
            'text'      => $cell,
            'css_class' => $class,
        ];
    }

    $maintable_rows[] = [
        'css_class'       => $row_class,
        'locale'          => $locale,
        'locamotion'      => $working_on_locamotion,
        'missing_strings' => $missing_strings,
        'cells'           => $cells,
    ];
}

$complete_locales_coverage = Utils::getUserBaseCoverage($complete_locales, LANG_CHECKER);
$average_coverage = round($sum_percent_covered_users / count($pages), 2);
$average_num_locales = round($sum_locales_per_page / count($pages), 2);
$project_info = [
    'average_coverage'  => $average_coverage,
    'average_locales'   => $average_num_locales,
    'complete_coverage' => $complete_locales_coverage,
    'complete_locales'  => count($complete_locales),
    'summary'           => $project_data['summary'],
    'title'             => $project_data['title'],
    'total_locales'     => count($project_locales),
];

// Summary table
$summary_rows = [];
if ($project_data['summary']) {
    foreach ($locales_summary as $stats) {
        $summary_rows[] = [
            'title'              => $stats['title'],
            'count_locales'      => count($stats['locales']),
            'description'        => $stats['description'],
            'locales'            => implode(', ', $stats['locales']),
            'locamotion_locales' => implode(', ', $stats['locamotion_locales']),
        ];
    }
}

print $twig->render(
    'project.twig',
    [
        'body_class'   => 'project_view',
        'project_info' => $project_info,
        'pages'        => $project_pages,
        'rows'         => $maintable_rows,
        'summary_rows' => $summary_rows,
    ]
);
