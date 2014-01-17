<?php
namespace Webdashboard;
// We always work with UTF8 encoding
mb_internal_encoding('UTF-8');

// Make sure we have a timezone set
date_default_timezone_set('Europe/Paris');

// Autoloading of classes (both /vendor and /classes)
require_once __DIR__ . '/../vendor/autoload.php';

define('CACHE', __DIR__ . '/../cache/');
const DEBUG = true;
const LANG_CHECKER = 'http://l10n.mozilla-community.org/~pascalc/langchecker/';

// For debugging
if (DEBUG) {
    error_reporting(E_ALL);
    \kint::enabled(true);
} else {
    \kint::enabled(false);
}