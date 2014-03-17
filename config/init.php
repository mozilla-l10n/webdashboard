<?php
namespace Webdashboard;

// We always work with UTF8 encoding
mb_internal_encoding('UTF-8');

// Make sure we have a timezone set
date_default_timezone_set('Europe/Paris');

// Autoloading of classes (both /vendor and /classes)
require_once __DIR__ . '/../vendor/autoload.php';

define('CACHE', __DIR__ . '/../cache/');
const DEBUG = false;
const LANG_CHECKER = 'http://l10n.mozilla-community.org/~pascalc/langchecker/';
const WEBPROJECTS_JSON = 'http://l10n.mozilla-community.org/~flod/webstatus/webstatus.json';

// For debugging
if (DEBUG) {
    error_reporting(E_ALL);
    \kint::enabled(true);
} else {
    \kint::enabled(false);
}

// this is the default template, views can define a different one
$template = 'default.php';
