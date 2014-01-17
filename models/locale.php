<?php
/*
 * Model for individual locale view
 */
namespace Webdashboard;

$json   = isset($_GET['json']) ? true : false;
$locale = $_GET['locale'];

// include all data about our locales
include __DIR__ . '/../data/locales.php';

// Check that this is a valid locale code called via GET
if (!isset($_GET['locale']) || !in_array($_GET['locale'], $locales)) {
    $content = '<h2>Wrong locale code</h2>';
    include __DIR__ . '/../views/error.php';
    return;
} else {
    $locale = $_GET['locale'];
}

// get lang files status from langchecker
$lang_files = Json::fetch(LANG_CHECKER . "?locale={$locale}&json");

// all opened bugs for a locale in the mozilla.org/l10n component
$bugzilla_query = 'https://bugzilla.mozilla.org/buglist.cgi?'
                . 'f1=cf_locale'
                . '&o1=equals'
                . '&query_format=advanced'
                . '&v1=' . urlencode(Utils::getBugzillaLocaleField($locale))
                . '&o2=equals'
                . '&f2=component'
                . '&v2=L10N'
                . '&bug_status=UNCONFIRMED'
                . '&bug_status=NEW'
                . '&bug_status=ASSIGNED'
                . '&bug_status=REOPENED'
                . '&classification=Other'
                . '&product=www.mozilla.org';


// cache in a local cache folder if possible
$csv = Utils::cacheUrl($bugzilla_query . '&ctype=csv', 15*60);

// generate all the bugs
$bugs = Utils::getBugsFromCSV($csv);

$rss_data = [];

foreach ($bugs as $k => $v) {
    $rss_data[] = [$k, "https://bugzilla.mozilla.org/show_bug.cgi?id={$k}", $v];
}

// d($lang_files);
// RSS feed  data
$total_missing_strings = 0;
$link = LANG_CHECKER .'?locale=' . $locale;

foreach ($lang_files as $site => $tablo) {
    foreach ($tablo as $file => $details) {
        $count = $details['identical'] + $details['missing'];
        if ($count > 0) {
            $message = "You have $count strings untranslated in $file";
            $status = (isset($details['critical']) && $details['critical'])
                      ? 'Priority file'
                      : 'Nice to have';
            $rss_data[] = array($status, $link, $message);
            $total_missing_strings += $count;
        }
    }
}

if ($total_missing_strings >0) {
    array_unshift(
        $rss_data,
        ['Total', $link, "You need to translate $total_missing_strings strings."]
    );
}

// Prepare a RSS feed
$rss = new Feed($rss_data);
$rss->title = "L10n Web Dashboard - ".$locale;
$rss->site  = "http://l10n.mozilla-community.org/webdashboard/?locale=".$locale;

include __DIR__ . '/../views/locale.php';
