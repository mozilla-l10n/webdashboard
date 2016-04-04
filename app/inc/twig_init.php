<?php

$templates = new Twig_Loader_Filesystem(__DIR__ . '/../templates/');
$options = [
    'cache' => false,
    // Enable only for debug (throws exception on missing variables)
    // 'strict_variables' => true,
];
$twig = new Twig_Environment($templates, $options);

// Global variables
$twig->addGlobal('langchecker_url', LANG_CHECKER);

// Functions
$langcheckerLinkNumber = new Twig_SimpleFunction('langcheckerLinkNumber', function ($number, $locale, $anchor) {
    if ($number > 0) {
        $url = LANG_CHECKER . "?locale={$locale}#{$anchor}";

        return "<a href=\"{$url}\">{$number}</a>";
    }

    return '-';
});

$twig->addFunction($langcheckerLinkNumber);
