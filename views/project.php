<?php
namespace Webdashboard;

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

foreach ($status as $locale => $array_status) {
    $total_page = $page_done = 0;
    $working_on_locamotion = in_array($locale, $locamotion);
    $content .= '<tr>' . "\n";
    $content .= "<td><a href=\"?locale=$locale\">$locale";
    if ($working_on_locamotion) {
        $content .= '<img src="./assets/images/locamotion_16.png" class="locamotion" />';
    }
    $content .= '</a></td>' . "\n";
    foreach ($array_status as $key => $result) {
        $cell = $class = '';

        // This locale does not have this page
        if ($result == 'none') {
            $cell = '1';
            $class = 'none';
        } else {
            $total_page++;
            // Page done
            if ($result['Identical'] == 0 && $result['Missing'] == 0) {
                $cell = '100%';
                $class = ' done';
                $page_done++;

            // Missing
            } elseif ($result['Translated'] == 0) {
                $cell = '0%';
                $class = ' missing';

            // In progress
            } else {
                $count = $result['Translated'] + $result['Missing'] + $result['Identical'];
                $cell = $result['Translated'] . '/' . $count;
                $class = ' inprogress';
            }
        }
        $content .= '<td class="' . $class . '">' . $cell . '</td>' . "\n";
    }
    $content .= '</tr>' . "\n";
    if ($page_done == $total_page) {
        $locale_done++;
    }
}
$content .= '</tbody>';
$content .= '</table>';

// Display bottom statistics
$content .= '<table class="results">';
$content .= '<tbody>';
$percent_locale_done = round($locale_done / $total_locales * 100, 2);
$total_col = $total_page + 1;
$content .= '<tr><td colspan="' . $total_col. '">' . $locale_done . '/'
    . $total_locales . ' perfect locales (' . $percent_locale_done . '%)</td></tr>';
$content .= '</tbody>';
$content .= '</table>';

include __DIR__ . '/../templates/' . $template;
