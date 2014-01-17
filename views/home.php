<?php

$content = '<div id="locales"><ul>';

foreach ($locales as $locale) {
    $content .= "<li><a href=\"./?locale={$locale}\">{$locale}</a></li>";
}

$content .= "</ul></div>";


include __DIR__ . '/../templates/' . $template;