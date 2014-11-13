<?php
namespace Webdashboard;

$body_class = $body_class . ' project';
$links = '<script language="javascript" type="text/javascript" src="./assets/js/sorttable.js"></script>' .
         '<script language="javascript" type="text/javascript" src="./assets/js/toggle.js"></script>';

// Display columns name
$header_row = $footer_row = '';
foreach ($pages as $page) {
    $status_url = LANG_CHECKER . '?locale=all&amp;website=' . $page['site'] . '&amp;file=' . $page['file'];
    $header_row .= "<th>{$page['description']}</th>\n";
    $footer_row .= "<td>
                      <a href='{$status_url}' title='Open the status page for this file'>{$page['file']}</a>
                    </td>\n";
}

$content = "
    <div id='hide_buttons'>
        <button class='button' id='button_locales' onclick='toggleLocales(\"complete_locale\", this.id)'>Hide completed locales</button>
        <button class='button' id='button_locamotion' onclick='toggleLocales(\"locamotion_locale\", this.id)'>Hide Locamotion locales</button>
        <p id='hidden_message' class='hidden'></p>
    </div>
    <table class='table sortable' id='project'>
        <caption>L10n Dashboard - {$project['title']}</caption>
        <thead>
            <tr>
                <th>Locale</th>
                <th>Missing<br/>strings</th>
                {$header_row}
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan='2'>&nbsp;</td>
                {$footer_row}
            </tr>
        </tfoot>
        <tbody>";

// Display status for all pages per locale
foreach ($status_formatted as $locale => $array_status) {
    $working_on_locamotion = in_array($locale, $locamotion);

    // Add extra class if locale is complete
    $row_class = $stats[$locale]['complete'] ? 'complete_locale' : '';

    if ($working_on_locamotion) {
        $row_class .= ' locamotion_locale';
    }

    $missing_strings = $stats[$locale]['strings_total'] - $stats[$locale]['strings_done'];
    if ($missing_strings == 0) {
        $missing_strings = '';
    }

    $content .= "<tr class='{$row_class}'>\n"
              . "<td class='locale'><a href='?locale={$locale}'>{$locale}";
    if ($working_on_locamotion) {
        $content .= '<img src="./assets/images/locamotion_16.png" class="locamotion" />';
    }
    $content .= "</a></td>\n"
              . "<td class='missing_strings'>{$missing_strings}</td>";
    foreach ($array_status as $key => $result) {
        $cell = $class = '';

        // This locale does not have this page
        if ($result == 'none') {
            $cell = '1';
            $class = $result;
        } else {
            // Page done
            if ($result  == 'done') {
                $cell = '100%';
                $class = $result;
            // Missing
            } elseif ($result == 'missing') {
                $cell = '0%';
                $class = $result;
            // In progress
            } else {
                $cell = $result;
                $class = 'inprogress';
            }
        }
        $content .= '<td class="' . $class . '">' . $cell . '</td>' . "\n";
    }
    $content .= '</tr>' . "\n";
}
$content .= '</tbody>'
          . '</table>';

// Display stats per page
$content .= '<table class="results">
                <thead>
                  <tr>
                    <th>Page</th>
                    <th>Completion</th>
                  </tr>
                </thead>
                <tbody>';

// Sort pages
ksort($locale_done_per_page);
foreach ($locale_done_per_page as $page => $locales) {
    $content .= '<tr><td class="results_file">' . $page_descriptions[$page] . ' <span class="filename">(' . $page . ')</span></td>'
              . '<td class="results_stats"> ' . count($locales) . '/'
              . count($locales_per_page[$page]) . ' perfect locales ('. $page_coverage[$page] . '%)</td></tr>';
}

// Display global stats
$content .= '<tr><th colspan="2" class="final">Total: ' . count($locale_done) . '/' . $total_locales . ' perfect locales (' . $perfect_locales_coverage . '%)</th></tr>'
          . '<tr><th colspan="2">Average: ' . $average_nb_locales . '/' . $total_locales . ' perfect locales (' . $average_coverage . '%)</th></tr>'
          . '</tbody>'
          . '</table>'
          . '<p class="table_legend">Percentages between parenthesis express coverage of our l10n base.</p>';

if ($project['summary']) {
    // Display table with status summary for locales
    $content .= '<table class="locales_summary">
                    <thead>
                      <tr>
                        <th>Status</th>
                        <th>Locales</th>
                        <th>On Locamotion</th>
                      </tr>
                    </thead>
                    <tbody>';
    foreach ($locales_summary as $stats) {
        $content .= "<tr>\n" .
                    "  <td class='category'>\n" .
                    "    <span class='title'>{$stats['title']} (" . count($stats['locales']) . ")</span>\n" .
                    "    <span class='description'>{$stats['description']}</span>\n" .
                    "  </td>\n" .
                    "  <td>" . implode(', ', $stats['locales']) . "</td>\n" .
                    "  <td>" . implode(', ', $stats['locamotion_locales']) . "</td>" .
                    "</tr>\n";
    }
    $content .= '   </tbody>
                </table>';

}

include __DIR__ . '/../templates/' . $template;
