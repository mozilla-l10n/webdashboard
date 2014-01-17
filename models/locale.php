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
if (!in_array($locale, $locales)) {
    $content = '<p>Wrong locale code</p>';
    include __DIR__ . '/../views/error.html';
    return;
}

// get lang files status from langchecker
$lang_files = Utils::getJsonArray(LANG_CHECKER . "?locale={$locale}&json");

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
$csv = Utils::cacheUrl($bugzilla_query . '&ctype=csv');

// generate all the bugs
$bugs = Utils::getBugsFromCSV($csv);

$rss_data = [];

foreach ($bugs as $k => $v) {
    $rss_data[] = [$k, "https://bugzilla.mozilla.org/show_bug.cgi?id={$k}", $v];
}

// RSS feed  data
foreach ($lang_files as $site => $tablo) {
    foreach ($tablo as $file => $details) {
        if ($details['identical'] + $details['missing'] > 0) {
            $message = 'You have ' . ($details['identical'] + $details['missing'])
                     . ' strings untranslated in ' . $file;
            $link = LANG_CHECKER .'?locale=' . $locale;
            $status = (isset($details['critical']) && $details['critical'])
                      ? 'Priority file: '
                      : 'Nice to have: ';
            $rss_data[] = array($status, $link, $message);
        }
    }
}

// d($rss_data); die;

// Prepare a RSS feed
$rss = new Feed($rss_data);
$rss->title = "L10n Web Dashboard - ".$locale;
$rss->site  = "http://l10n.mozilla-community.org/webdashboard/?locale=".$locale;

include __DIR__ . '/../views/locale.php';
