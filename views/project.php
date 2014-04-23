<?php
namespace Webdashboard;

$body_class = $body_class . ' project';
$links = '<script language="javascript" type="text/javascript" src="./assets/js/sorttable.js"></script>';
$content = "
    <table class=\"table sortable\" id=\"project\">
        <caption>L10n Project Dashboard ($project)</caption>
        <thead>
            <tr>
                <th>Locale</th>";

// Display columns name
foreach ($pages as $page) {
    $content .= '<td>' . $page['file'] . '</td>';
}
$content .= '
            </tr>
        </thead>
        <tbody>';

// Display status for all pages per locale
foreach ($status_formated as $locale => $array_status) {
    $working_on_locamotion = in_array($locale, $locamotion);
    $content .= '<tr>' . "\n"
              . "<td><a href=\"?locale=$locale\">$locale";
    if ($working_on_locamotion) {
        $content .= '<img src="./assets/images/locamotion_16.png" class="locamotion" />';
    }
    $content .= '</a></td>' . "\n";
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
$content .= '<table class="results sortable">
                <thead>
                  <tr>
                    <th>Page</th><th>Completion</th>
                  </tr>
                </thead>
                <tbody>';

foreach ($locale_done_per_page as $page => $locales) {
    $content .= '<tr><td colspan="1">' . $page . '</td>'
              . '<td colspan="1"> ' . count($locales) . '/'
              . $total_locales . ' perfect locales ('. $page_coverage[$page] . '%)</td></tr>';
}

// Display global stats
$content .= '<tr><td colspan="2" class="final">Total: ' . count($locale_done) . '/' . $total_locales . ' perfect locales (' . $perfect_locales_coverage . '%)</td></tr>'
          . '<tr><td colspan="2">Average: ' . $average_nb_locales . '/' . $total_locales . ' perfect locales (' . $average_coverage . '%)</td></tr>'
          . '</tbody>'
          . '</table>';

include __DIR__ . '/../templates/' . $template;
