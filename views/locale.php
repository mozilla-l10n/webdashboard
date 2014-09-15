<?php
namespace Webdashboard;

$rss_status = <<<RSS
<p class="feed">
    <a href="?locale={$locale}&amp;rss">
    <img src="./assets/images/icon-rss-48x48.png" alt="rss"/>
    Subscribe to the RSS feed for your locale!
    </a>
</p>
<div id="locale">
    <a href="http://wiki.mozilla.org/L10n:Teams:{$locale}">{$locale}</a>
RSS;
if ($locamotion) {
    $rss_status .= '<img src="assets/images/locamotion.png" class="locamotion" />';
}
$rss_status .= '</div>';

$lang_files_status = '<h2>State of your lang files <small>(data updated every 15 minutes)</small></h2>';
$locale_missing = 0;

foreach ($lang_files as $site => $site_files) {
    $rows = '';
    $site_missing = 0;

    foreach ($site_files as $file => $details) {
        $file_missing = $details['identical'] + $details['missing'];
        if ($file_missing > 0) {
            // File has missing strings (identical or actually missing)
            $site_missing += $file_missing;
            $locale_missing += $file_missing;

            // Determine critical status
            $critical = (isset($details['critical']) && $details['critical']) ? '<strong>Yes</strong>' : 'No';

            // Determine deadline status
            $deadline_class = '';
            if (isset($details['deadline'])) {
                $deadline_timestamp = (new \DateTime($details['deadline']))->getTimestamp();
                $deadline = date('F d', $deadline_timestamp);
                if ($deadline_timestamp < time()) {
                    $deadline = date('F d Y', $deadline_timestamp);
                    $deadline_class = 'overdue';
                }
            } else {
                $deadline = '--';
            }

            $url = LANG_CHECKER . "?locale={$locale}#{$file}";
            $rows .= "  <tr>\n" .
                     "    <th class='maincolumn'><a href='{$url}'>{$file}</a></th>\n" .
                     "    <td><a href='{$url}'>{$file_missing}</a></td>" .
                     "    <td class='{$deadline_class}'>{$deadline}</td>\n" .
                     "    <td>{$critical}</td>\n" .
                     "  </tr>\n";
        }
    }

    if ($site_missing > 0) {
        $lang_files_status .= "\n<table class='file_detail'>\n" .
                              "  <tr>\n" .
                              "    <th class='maincolumn'>{$site}</th>\n" .
                              "    <th>Not fully translated</th>\n" .
                              "    <th>Deadline</th>\n" .
                              "    <th>Critical</th>\n" .
                              "  </tr>\n" .
                              $rows .
                              "</table>\n";
    } else {
        $lang_files_status .= "\n<table>\n" .
                              "  <tr>\n" .
                              "    <th class='maincolumn'>{$site}</th>\n" .
                              "    <th><span style='color:gray'>All Files translated</span></th>\n" .
                              "  </tr>\n" .
                              "</table>\n";
    }
}

$lang_files_status .= "<p><small>Reminder: Your staging site for mozilla.org/{$locale}/ is
                       <a href='https://www-dev.allizom.org/{$locale}'>www-dev.allizom.org/{$locale}/</a></small></p>";

ob_start();
echo '<h2>Open bugs for your locale:</h2>';
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

$weblocale = $locale;
if ($locale == 'es-ES') {
    // Use 'es' instead of 'es-ES' for web projects
    $weblocale = 'es';
}
echo "<h2>External Web Projects Status ({$weblocale})</h2>\n";
echo "<p>Hover your mouse on a cell in the <em>Status</em> column to display statistics or errors for a specific project.<br/>
         Data updated about every 5 hours. Last update: {$webprojects['metadata']['creation_date']}.</p>\n";
if (isset($webprojects[$weblocale])) {
    // Generate list of products for this locale and sort them by name
    $available_products = [];
    foreach (array_keys($webprojects[$weblocale]) as $product_code) {
        $available_products[$product_code] = $webprojects[$weblocale][$product_code]['name'];
    }
    asort($available_products);
    echo "
<table class='webprojects'>
  <thead>
    <tr>
      <th>Project</th>
      <th>%</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>\n";
    foreach ($available_products as $product_code => $product_name) {
        $webproject = $webprojects[$weblocale][$product_code];

        // Initialize values
        $untrans_width = 0;
        $fuzzy_width = 0;
        $errors_width = 0;
        $trans_width = 0;

        if ($webproject['error_status']) {
            // File has errors
            $errors_width = 100;
            $message = $webproject['error_message'];
        } elseif ($webproject['total'] === 0) {
            // File is empty
            $message = "File is empty (no strings)";
        } else {
            $untrans_width = floor($webproject['untranslated'] / $webproject['total'] * 100);
            $fuzzy_width = floor($webproject['fuzzy'] / $webproject['total'] * 100);
            $message = "{$webproject['translated']} translated, {$webproject['untranslated']} untranslated, {$webproject['fuzzy']} fuzzy";
        }

        if ($errors_width === 0) {
            $trans_width = 100 - $fuzzy_width - $untrans_width;
        }

        echo  "
    <tr>
      <td>{$webproject['name']}</td>
      <td class='perc'>{$webproject['percentage']}</td>
      <td class='status' title='{$message}'>
        <span class='wp_status wp_errors' style='width: {$errors_width}%;'>file contains errors</span>
        <span class='wp_status wp_trans' style='width: {$trans_width}%;'></span>
        <span class='wp_status wp_untrans' style='width: {$untrans_width}%;'></span>
        <span class='wp_status wp_fuzzy' style='width: {$fuzzy_width}%;'></span>
      </td>
    </tr>\n";

    }
    echo "  </tbody>\n</table>\n";
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
