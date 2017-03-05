<?php
namespace Webdashboard;

// Build RSS data
$rss_data = [];
if (count($bugs) > 0) {
    foreach ($bugs as $bug_number => $bug_description) {
        $rss_data[] = [
            "Bug {$bug_number}: {$bug_description}",
            "https://bugzilla.mozilla.org/show_bug.cgi?id={$bug_number}",
            $bug_description,
        ];
    }
}

$total_missing_strings = 0;
$total_errors = 0;
$total_missing_files = 0;
$link = LANG_CHECKER . "?locale={$locale}";

foreach ($lang_files as $site => $site_files) {
    foreach ($site_files as $file => $details) {
        $message = '';

        if ($details['data_source'] == 'lang') {
            // Standard lang file
            $count = $details['identical'] + $details['missing'];
            if ($count > 0) {
                $message = 'You have ' . Utils::getPluralForm($count, 'untranslated string') . " in {$file}";
                $total_missing_strings += $count;
            }
            if ($details['errors'] > 0) {
                $message = 'You have ' . Utils::getPluralForm($details['errors'], 'error') . " in {$file}";
                $total_errors += $details['errors'];
            }
        } else {
            // Raw file with only a generic status
            $cmp_status = $details['status'];
            $file_flags = isset($details['flags']) ? $details['flags'] : [];
            if ($cmp_status == 'untranslated' || $cmp_status == 'outdated') {
                $message = "{$file} needs to be updated.";
                $total_missing_files++;
            } elseif (($cmp_status == 'missing_locale' || $cmp_status == 'missing_reference') &&
                ! in_array('optional', $file_flags)) {
                // Display warning for a missing file only if it's not optional
                $message = "{$file} is missing.";
                $total_missing_files++;
            }
        }

        if ($message != '') {
            $status = Utils::getPriorityDesc($details['priority']);
            if (isset($details['deadline']) && $details['deadline']) {
                $deadline = date('F d', (new \DateTime($details['deadline']))->getTimestamp());
                $status .= ' (deadline is ' . $deadline . ')';
            }
            $rss_data[] = ["{$status}: {$message}", $link, $message];
        }
    }
}

if ($total_missing_files > 0) {
    $tmp_message = 'You need to update ' . Utils::getPluralForm($total_missing_files, 'file') . '.';
    array_unshift(
        $rss_data,
        ["Other files: {$tmp_message}", $link, $tmp_message]
    );
}

if ($total_missing_strings > 0) {
    $tmp_message = 'You need to translate ' . Utils::getPluralForm($total_missing_strings, 'string') . '.';
    array_unshift(
        $rss_data,
        ["Missing strings: {$tmp_message}", $link, $tmp_message]
    );
}

if ($total_errors > 0) {
    $tmp_message = 'You need to fix ' . Utils::getPluralForm($total_errors, 'error') . '.';
    array_unshift(
        $rss_data,
        ["Errors: {$tmp_message}", $link, $tmp_message]
    );
}

// Add information about external web projects
if ($locale_has_web_projects) {
    $link = "https://l10n.mozilla-community.org/webdashboard/?locale={$locale}#web_projects";
    $tmp_message = '';
    foreach ($available_web_projects as $product_code => $product_name) {
        $webproject = $webprojects['locales'][$locale][$product_code];
        $webproject_errors = $webproject['error_status'];
        $webproject_incomplete = $webproject['missing'] + $webproject['untranslated'] > 0;
        if ($webproject_incomplete || $webproject_errors) {
            // Web project is incomplete (either missing or untranslated strings)
            $tmp_message .= "<p><strong>{$webproject['name']}</strong><br/>";
            $tmp_message .= Utils::getPluralForm($webproject['missing'], 'missing string') . ', ';
            $tmp_message .= Utils::getPluralForm($webproject['untranslated'], 'untranslated string') . ', ';
            $tmp_message .= Utils::getPluralForm($webproject['fuzzy'], 'fuzzy string') . '.';
            if ($webproject_errors) {
                $tmp_message .= '<br/>' . htmlspecialchars($webproject['error_message']);
            }
            $tmp_message .= '</p>';
        }
    }
    if ($tmp_message != '') {
        array_push(
            $rss_data,
            ['Incomplete Web Projects', $link, $tmp_message]
        );
    }
}

// Build the RSS feed, print it and stop execution
$rss_feed = new FeedRSS(
    "L10n Web Dashboard - {$locale}",
    "https://l10n.mozilla-community.org/webdashboard/?locale={$locale}",
    "[{$locale}] Localization Status of Web Content",
    $rss_data
);
print $rss_feed->buildRSS();
