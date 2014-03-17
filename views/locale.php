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
</div>
RSS;

$lang_files_status = '<h2>State of your lang files <small>(data updated every 15 minutes)</small></h2>';
$total_identical = 0;
foreach ($lang_files as $site => $tablo) {

    $rows = '';
    $local_count_identical = 0;

    foreach ($tablo as $file => $details) {

        $extra_class1 = '';
        $extra_class2 = '';

        if ($details['identical'] == 0 && $details['missing'] == 0) {
            $extra_class1 = 'class="hideme"';
        }

        $rows .= "
                    <tr $extra_class1>
                        <th class=\"clickme \"><a href=\"". LANG_CHECKER . "?locale=$locale#$file\">$file</a></th>";

        if ($details['identical'] == 0) {
            $rows .= "     <td class=\"col2\">$details[identical]</td>";
        } else {
            $rows .= "     <td class=\"col2\"><a href=\"". LANG_CHECKER . "?locale=$locale#$file\">$details[identical]</a></td>";
            $local_count_identical += $details['identical'];
            $total_identical += $details['identical'];
        }

        if ($details['missing'] > 0) {
            $local_count_identical += $details['missing'];
            $total_missing += $details['missing'];
        }

        $critical = (isset($details['critical']) && $details['critical']) ? 'Yes' : 'No';

        if (isset($details['critical'])) {
            $critical = "<strong>$critical</strong>";
        }
        $rows .= '     <td class="col3">'
                . $critical
                . '</td>';
        $rows .= "</tr>";

    }

    $extra_class2 = ($local_count_identical == 0) ? $extra_class1 : '';
    $message = ($local_count_identical == 0) ? '<span style="color:gray">All Files translated</span>' : 'Not fully translated';

    $lang_files_status .= "<table>
        <tr>
            <th class=\"col1 clickme\">$site</th>
            <th class=\"col2\">$message</th>
            <th $extra_class2>Critical</th>

        </tr>";

    $lang_files_status .= $rows;
    $lang_files_status .= "</table>";

}
$lang_files_status .= "<p><small>Reminder: Your staging site for mozilla.org/{$locale}/ is
                       <a href=\"https://www-dev.allizom.org/{$locale}\">
                       www-dev.allizom.org/{$locale}/
                       </a></small></p>";
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
    // Use 'es' instead of 'es-ES' for webprojects
    $weblocale = 'es';
}
echo "<h2>External Web Projects Status ({$weblocale})</h2>\n";
if (count($webprojects) > 0) {
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
    foreach ($webprojects[$weblocale] as $webproject) {
        $status_row = function() use ($webproject) {
            $untrans_width = floor($webproject['untranslated']/$webproject['total']*100);
            $fuzzy_width = floor($webproject['fuzzy']/$webproject['total']*100);
            $trans_width = 100 - $fuzzy_width - $untrans_width;
            $row = "<span class='wp_status wp_trans' title='{$webproject['translated']} translated strings' style='width: {$trans_width}%;'></span>";
            $row .= "<span class='wp_status wp_untrans' title='{$webproject['untranslated']} untranslated strings'  style='width: {$untrans_width}%;'></span>";
            $row .= "<span class='wp_status wp_fuzzy' title='{$webproject['fuzzy']} fuzzy strings' style='width: {$fuzzy_width}%;'></span>";
            return $row;
        };

        echo "    <tr>\n";
        echo "      <td>{$webproject['name']}</td>\n";
        echo "      <td class='perc'>{$webproject['percentage']}</td>\n";
        echo "      <td class='status'>" . $status_row() . "</td>\n";
        echo "</tr>\n";
    }
    echo "  </tbody>\n</table>\n";
} else {
    echo '<p>Data not available.</p>';
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
