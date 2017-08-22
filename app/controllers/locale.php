<?php
namespace Webdashboard;

use Bugzilla\Bugzilla;
use Cache\Cache;

// Check if the locale requested via GET is a supported locale
if (! in_array($locale, $locales)) {
    print $twig->render(
        'error.twig',
        [
            'body_class' => 'error',
            'locale'     => $locale,
        ]
    );

    return;
}

// Get lang files status from Langchecker
$lang_files = $json_object
    ->setURI(LANG_CHECKER . "?locale={$locale}&json")
    ->fetchContent();

// All open bugs for this locale in the mozilla.org/l10n component
$bugzilla_query_mozillaorg = 'https://bugzilla.mozilla.org/buglist.cgi?'
                           . 'f1=cf_locale'
                           . '&o1=equals'
                           . '&query_format=advanced'
                           . '&v1=' . urlencode(Bugzilla::getBugzillaLocaleField($locale))
                           . '&o2=equals'
                           . '&f2=component'
                           . '&v2=L10N'
                           . '&bug_status=UNCONFIRMED'
                           . '&bug_status=NEW'
                           . '&bug_status=ASSIGNED'
                           . '&bug_status=REOPENED'
                           . '&classification=Other'
                           . '&product=www.mozilla.org';

// All open bugs for this locale in the Mozilla Localization/locale component, with "webdashboard" in the whiteboard
$bugzilla_query_l10ncomponent = 'https://bugzilla.mozilla.org/buglist.cgi?'
                              . '&query_format=advanced'
                              . '&status_whiteboard_type=allwordssubstr'
                              . '&status_whiteboard=webdashboard'
                              . '&bug_status=UNCONFIRMED'
                              . '&bug_status=NEW'
                              . '&bug_status=ASSIGNED'
                              . '&bug_status=REOPENED'
                              . '&component=' . urlencode(Bugzilla::getBugzillaLocaleField($locale, 'l10n'))
                              . '&classification=Client%20Software'
                              . '&product=Mozilla%20Localizations';

/*
    Use cached requests if available, cache expires after 1 hour.
    Since the result can be empty, I need to check strictly for false.
*/
$cache_id = "bugs_mozillaorg_{$locale}";
$bugs_mozillaorg = Cache::getKey($cache_id, 60 * 60);
if ($bugs_mozillaorg === false) {
    $csv_mozillaorg = file($bugzilla_query_mozillaorg . '&ctype=csv');
    $bugs_mozillaorg = Bugzilla::getBugsFromCSV($csv_mozillaorg);
    Cache::setKey($cache_id, $bugs_mozillaorg);
}

$cache_id = "bugs_l10ncomponent_{$locale}";
$bugs_l10ncomponent = Cache::getKey($cache_id, 60 * 60);
if ($bugs_l10ncomponent === false) {
    $csv_l10ncomponent = file($bugzilla_query_l10ncomponent . '&ctype=csv');
    $bugs_l10ncomponent = Bugzilla::getBugsFromCSV($csv_l10ncomponent);
    Cache::setKey($cache_id, $bugs_l10ncomponent);
}

$bugs = $bugs_mozillaorg + $bugs_l10ncomponent;

// Read status of external web projects, cache expires after 1 hour
$cache_id = 'external_webprojects';
if (! $webprojects = Cache::getKey($cache_id, 60 * 60)) {
    $webprojects = $json_object
        ->setURI(WEBPROJECTS_JSON)
        ->fetchContent();
    Cache::setKey($cache_id, $webprojects);
}
$locale_has_web_projects = isset($webprojects['locales'][$locale]);
$last_update_web_projects = date('Y-m-d H:i e (O)', strtotime($webprojects['metadata']['creation_date']));

// Generate a list of products for this locale and sort them by name
$available_web_projects = [];
if ($locale_has_web_projects) {
    foreach (array_keys($webprojects['locales'][$locale]) as $product_code) {
        $available_web_projects[$product_code] = $webprojects['locales'][$locale][$product_code]['name'];
    }
    asort($available_web_projects);
}

// If RSS was requested, print the RSS and stop execution
if ($rss) {
    include __DIR__ . '/rss.php';

    return;
}

// Generate data for web projects
$locale_web_projects = [];
foreach ($available_web_projects as $product_code => $product_name) {
    $webproject = $webprojects['locales'][$locale][$product_code];

    // Initialize values
    $errors_width = 0;
    $fuzzy_width = 0;
    $identical_width = 0;
    $trans_width = 0;
    $untrans_width = 0;

    if ($webproject['error_status']) {
        // File has errors
        $errors_width = 100;
        $message = str_replace('\'', '"', htmlspecialchars($webproject['error_message']));
    } elseif ($webproject['total'] === 0) {
        // File is empty
        $message = 'File is empty (no strings)';
    } else {
        if (in_array($webproject['source_type'], ['l20n', 'properties'])) {
            // Web project based on .properties or .ftl (l20n)
            $fuzzy_width = 0;
            $identical_width = floor($webproject['identical'] / $webproject['total'] * 100);
            $untrans_width = floor($webproject['missing'] / $webproject['total'] * 100);
            $message = "{$webproject['translated']} translated, {$webproject['missing']} missing, {$webproject['identical']} identical";
        } elseif ($webproject['source_type'] == 'xliff') {
            // Web project based on .xliff files
            $fuzzy_width = 0;
            $total = $webproject['total'] + $webproject['missing'];
            $missing = $webproject['untranslated'] + $webproject['missing'];
            $identical_width = floor($webproject['identical'] / $total * 100);
            $untrans_width = floor($missing / $total * 100);
            $message = "{$webproject['translated']} translated, {$webproject['missing']} missing, {$webproject['untranslated']} untranslated, {$webproject['identical']} identical";
        } else {
            // Web project based on .po files (default)
            $fuzzy_width = floor($webproject['fuzzy'] / $webproject['total'] * 100);
            $untrans_width = floor($webproject['untranslated'] / $webproject['total'] * 100);
            $message = "{$webproject['translated']} translated, {$webproject['untranslated']} untranslated, {$webproject['fuzzy']} fuzzy";
        }
    }

    if ($errors_width === 0) {
        $trans_width = 100 - $fuzzy_width - $identical_width - $untrans_width;
    }

    $locale_web_projects[$product_code] = [
        'errors_width'    => $errors_width,
        'fuzzy_width'     => $fuzzy_width,
        'identical_width' => $identical_width,
        'message'         => $message,
        'name'            => $product_name,
        'percentage'      => $webproject['percentage'],
        'trans_width'     => $trans_width,
        'untrans_width'   => $untrans_width,
    ];
}

$lang_files_status = [];
foreach ($lang_files as $site => $site_files) {
    /*
        The type of data source is identical for all files in a website, so
        I can just consider the first element. If the list of files is empty,
        I use 'lang' as default.
    */
    $data_source_type = ! empty($site_files) ?
        reset($site_files)['data_source'] :
        'lang';

    $lang_files_status[$site] = [
        'files'       => [],
        'source_type' => $data_source_type,
    ];

    foreach ($site_files as $file => $details) {
        // Determine priority
        $priority = $details['priority'];

        // Determine deadline
        $deadline_class = '';
        if (isset($details['deadline'])) {
            $deadline_timestamp = (new \DateTime($details['deadline']))->getTimestamp();
            $deadline = date('F d', $deadline_timestamp);
            $last_week = $deadline_timestamp - 604800; // 7 days (60 * 60 * 24 * 7)
            $current_time = time();
            if ($deadline_timestamp < $current_time) {
                $deadline = date('F d Y', $deadline_timestamp);
                $deadline_class = 'deadline_overdue';
            } elseif ($last_week < $current_time) {
                $deadline_class = 'deadline_closing';
            }
        } else {
            $deadline = '-';
        }

        if ($details['data_source'] == 'lang') {
            if ($details['errors'] + $details['identical'] + $details['missing'] > 0) {
                $lang_files_status[$site]['files'][$file] = [
                    'priority'       => $priority,
                    'priority_desc'  => Utils::getPriorityDesc($priority),
                    'deadline_class' => $deadline_class,
                    'deadline'       => $deadline,
                    'errors'         => $details['errors'],
                    'missing'        => $details['identical'] + $details['missing'],
                    'missing_words'  => $details['missing_words'],
                    'pontoon_link'   => isset($details['pontoon_link']) ? $details['pontoon_link'] : '',
                ];
            }
        } else {
            $cmp_status = $details['status'];
            $file_flags = isset($details['flags']) ? $details['flags'] : [];

            /*
                We display a file only if it's untranslated or outdated, other
                cases are displayed only if this file is not optional.
            */
            $hide_file = true;

            if ($cmp_status == 'untranslated' || $cmp_status == 'outdated') {
                $hide_file = false;
            } elseif (($cmp_status == 'missing_locale' || $cmp_status == 'missing_reference') &&
                ! in_array('optional', $file_flags)) {
                // File is missing and it's not optional
                $hide_file = false;
            }

            if (! $hide_file) {
                $lang_files_status[$site]['files'][$file] = [
                    'priority'       => $priority,
                    'priority_desc'  => Utils::getPriorityDesc($priority),
                    'deadline_class' => $deadline_class,
                    'deadline'       => $deadline,
                    'status_class'   => "raw_status raw_{$cmp_status}",
                    'status_text'    => str_replace('_', ' ', $cmp_status),
                ];
            }
        }
    }
}

print $twig->render(
    'locale.twig',
    [
        'body_class'          => 'locale',
        'bugs'                => $bugs,
        'lang_files_status'   => $lang_files_status,
        'locale'              => $locale,
        'mozilla_org'         => isset($lang_files['www.mozilla.org']),
        'web_projects'        => $locale_web_projects,
        'web_projects_update' => $last_update_web_projects,
    ]
);
