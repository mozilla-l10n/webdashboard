<?php
namespace Webdashboard;

$rss_status = <<<RSS
<p class="feed">
    <a href="?locale={$locale}&amp;rss">
    <img src="./assets/images/icon-rss-48x48.png" alt="rss"/>
    Subscribe to the RSS feed for your locale!
    </a>
</p>
<div id="locale_page_title">
    <a href="http://wiki.mozilla.org/L10n:Teams:{$locale}">{$locale}</a>
RSS;
if ($locamotion) {
    $rss_status .= '<img src="assets/images/locamotion.png" class="locamotion" />';
}
$rss_status .= '</div>';

$lang_files_status = '<h2 class="title_anchor" id="lang_status"><a href="#lang_status">#</a>State of your lang files <small>(data updated every 15 minutes)</small></h2>';

foreach ($lang_files as $site => $site_files) {
    $rows = '';
    $display_errors = false;

    foreach ($site_files as $file => $details) {
        // Determine critical status
        $critical = (isset($details['critical']) && $details['critical']) ? '<strong>Yes</strong>' : 'No';

        // Determine deadline status
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
            // Standard .lang file
            $file_missing = $details['identical'] + $details['missing'];
            $file_errors = $details['errors'];
            if ($file_missing + $file_errors > 0) {
                // File has missing strings (identical or actually missing)
                $display_errors = true;
                $url = LANG_CHECKER . "?locale={$locale}#{$file}";
                if ($file_errors > 0) {
                    $error_display = "<a href='{$url}'>{$file_errors}</a>";
                } else {
                    $error_display = '-';
                }
                if ($file_missing > 0) {
                    $missing_display = "<a href='{$url}'>{$file_missing}</a>";
                } else {
                    $missing_display = '-';
                }
                $rows .= "  <tr>\n" .
                         "    <th class='main_column'><a href='{$url}'>{$file}</a></th>\n" .
                         "    <td>{$missing_display}</td>\n" .
                         "    <td>{$error_display}</td>\n" .
                         "    <td class='{$deadline_class}'>{$deadline}</td>\n" .
                         "    <td>{$critical}</td>\n" .
                         "  </tr>\n";
            }
        } else {
            // Raw file with only a generic status
            $url = LANG_CHECKER . "?locale={$locale}#{$site}";
            $cmp_status = $details['status'];
            $file_flags = isset($details['flags']) ? $details['flags'] : [];

            // We display a file only if it's untranslated or outdated, other cases
            // are displayed only if file is not optional
            $hide_file = true;

            if ($cmp_status == 'untranslated' || $cmp_status == 'outdated') {
                $hide_file = false;
            } elseif (($cmp_status == 'missing_locale' || $cmp_status == 'missing_reference') &&
                ! in_array('optional', $file_flags)) {
                // File is missing and it's not optional
                $hide_file = false;
            }

            if (! $hide_file) {
                // Display warnings only if the file is not optional
                $rows .= "  <tr>\n" .
                         "    <th class='main_column'><a href='{$url}'>{$file}</a></th>\n" .
                         "    <td><span class='raw_status raw_{$cmp_status}'>" . str_replace('_', ' ', $cmp_status) . "</span></td>" .
                         "    <td class='{$deadline_class}'>{$deadline}</td>\n" .
                         "    <td>{$critical}</td>\n" .
                         "  </tr>\n";
                $display_errors = true;
            }
        }
    }

    if ($display_errors) {
        /* The type of data source is identical for all files in a website.
         * Since I have errors, there's at least one file that I can use to
         * determine the data source type, no need to check if it exists.
         */
        $data_source_type = array_shift($site_files)['data_source'];

        if ($data_source_type == 'lang') {
            // Standard .lang file
            $lang_files_status .= "\n<table class='file_detail'>\n" .
                                  "  <tr>\n" .
                                  "    <th class='main_column'>{$site}</th>\n" .
                                  "    <th>Missing</th>\n" .
                                  "    <th>Errors</th>\n" .
                                  "    <th>Deadline</th>\n" .
                                  "    <th>Critical</th>\n" .
                                  "  </tr>\n" .
                                  $rows .
                                  "</table>\n";
        } else {
            // Raw file with only a generic status
            $lang_files_status .= "\n<table class='file_detail'>\n" .
                                  "  <tr>\n" .
                                  "    <th class='main_column'>{$site}</th>\n" .
                                  "    <th>Status</th>\n" .
                                  "    <th>Deadline</th>\n" .
                                  "    <th>Critical</th>\n" .
                                  "  </tr>\n" .
                                  $rows .
                                  "</table>\n";
        }
    } else {
        $lang_files_status .= "\n<table>\n" .
                              "  <tr>\n" .
                              "    <th class='main_column'>{$site}</th>\n" .
                              "    <th><span style='color:gray'>All Files translated</span></th>\n" .
                              "  </tr>\n" .
                              "</table>\n";
    }
}

if (isset($lang_files['www.mozilla.org'])) {
    $optin_url = LANG_CHECKER . "?action=optin&locale={$locale}";
    $lang_files_status .= "<p><small>Reminder: Your staging site for mozilla.org/{$locale}/ is " .
                          "<a href='https://www-dev.allizom.org/{$locale}'>www-dev.allizom.org/{$locale}/</a><br/>" .
                          "The list of opt-in pages for mozilla.org is available <a href='{$optin_url}'>here</a>.</small></p>";
}

if (count($lang_files) == 0) {
    $lang_files_status .= "<p>There are no files tracked for this locale at the moment.</p>";
}

ob_start();
echo '<h2 class="title_anchor" id="bugs"><a href="#bugs">#</a>Open bugs for your locale:</h2>';
echo '<ul>';

if (count($bugs) > 0) {
    foreach ($bugs as $bug_number => $bug_title) {
        echo "<li><a href='https://bugzilla.mozilla.org/show_bug.cgi?id={$bug_number}'>{$bug_number}: {$bug_title}</a></li>";
    }
} else {
    echo '<li>No bugs. Good job!</li>';
}

echo '</ul>';

$bugs_status = ob_get_contents();
ob_end_clean();

ob_start();

echo "<h2 class=\"title_anchor\" id=\"web_projects\"><a href=\"#web_projects\">#</a>External Web Projects Status ({$locale})</h2>\n";
if (isset($webprojects['locales'][$locale])) {
    echo "<p>Hover your mouse on a cell in the <em>Status</em> column to display statistics or errors for a specific project.<br/>
          <small>Data updated about every 3 hours. Last update: {$webprojects['metadata']['creation_date']}.</small></p>\n";
    // Generate list of products for this locale and sort them by name
    $available_products = [];
    foreach (array_keys($webprojects['locales'][$locale]) as $product_code) {
        $available_products[$product_code] = $webprojects['locales'][$locale][$product_code]['name'];
    }
    asort($available_products);
    echo "
<table class='web_projects'>
  <thead>
    <tr>
      <th>Project</th>
      <th>%</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>\n";
    foreach ($available_products as $product_code => $product_name) {
        $webproject = $webprojects['locales'][$locale][$product_code];

        // Initialize values
        $untrans_width = 0;
        $fuzzy_width = 0;
        $errors_width = 0;
        $trans_width = 0;

        if ($webproject['error_status']) {
            // File has errors
            $errors_width = 100;
            $message = str_replace('\'', '"', htmlspecialchars($webproject['error_message']));
        } elseif ($webproject['total'] === 0) {
            // File is empty
            $message = "File is empty (no strings)";
        } else {
            if ($webproject['source_type'] == 'properties') {
                // Web project based on .properties
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
                $identical_width = 0;
                $untrans_width = floor($webproject['untranslated'] / $webproject['total'] * 100);
                $message = "{$webproject['translated']} translated, {$webproject['untranslated']} untranslated, {$webproject['fuzzy']} fuzzy";
            }
        }

        if ($errors_width === 0) {
            $trans_width = 100 - $fuzzy_width - $identical_width - $untrans_width;
        }

        echo  "
    <tr>
      <td>{$webproject['name']}</td>
      <td class='perc'>{$webproject['percentage']}</td>
      <td class='status' title='{$message}'>
        <span class='web_projects_status web_projects_errors' style='width: {$errors_width}%;'>error</span>
        <span class='web_projects_status web_projects_trans' style='width: {$trans_width}%;'></span>
        <span class='web_projects_status web_projects_untrans' style='width: {$untrans_width}%;'></span>
        <span class='web_projects_status web_projects_fuzzy' style='width: {$fuzzy_width}%;'></span>
        <span class='web_projects_status web_projects_identical' style='width: {$identical_width}%;'></span>
      </td>
    </tr>\n";
    }
    echo "  </tbody>\n</table>\n" .
         "  <p><small>An alternative view for web projects is <a href='https://l10n.mozilla-community.org/~flod/webstatus/?locale={$locale}'>available in this page</a>.</small></p>\n";
} else {
    echo '<p>There are no web projects available for this locale.</p>';
}
$webprojects_status = ob_get_contents();
ob_end_clean();

// build the content based on the various blocks we just created
$content = $rss_status . $lang_files_status . $bugs_status . $webprojects_status;

/*
if we ask for an rss page, we just pass the $rss object created
in the model that contains the data we want to the object renderer
 */
if (!isset($_GET['rss'])) {
    include __DIR__ . '/../templates/' . $template;
} else {
    print $rss->buildRSS();
}
