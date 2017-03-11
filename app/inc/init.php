<?php
namespace Webdashboard;

use Cache\Cache;
use Json\Json;

// We always work with UTF8 encoding
mb_internal_encoding('UTF-8');

// Make sure we have a timezone set
date_default_timezone_set('Europe/Paris');

// Autoloading of classes (both /vendor and /classes)
require_once __DIR__ . '/../../vendor/autoload.php';

$settings_filename = __DIR__ . '/../config/settings.inc.php';
if (file_exists($settings_filename)) {
    require $settings_filename;
} else {
    echo '<h1>Error: missing config file</h1>';
    echo "<p>Config file is missing: <code>{$settings_filename}</code></p>";
    echo '<p>Rename <strong>app/config/settings.inc.php.ini</strong> as <strong>app/config/settings.inc.php</strong> to get started.</p>';
    exit;
}

// Cache class
define('CACHE_ENABLED', true);
define('CACHE_PATH', __DIR__ . '/../../cache/');   // This folder needs to be writable by PHP
define('CACHE_TIME', 15 * 60);    // Default: 15 minutes

// Get the list of all supported locales from Langchecker
$json_locales = new Json;
$cache_id = 'webdashboard_locales';
if (! $locales = Cache::getKey($cache_id, 60 * 60)) {
    $locales = $json_locales
        ->setURI(LANG_CHECKER . '?action=listlocales&project=langchecker&json')
        ->fetchContent();
    Cache::setKey($cache_id, $locales);
}
unset($json_locales);
if (empty($locales)) {
    echo '<h1>Error: missing list of supported locales</h1>';
    echo '<p>There was an error retrieving the list of supported locales from Langchecker. Please check the configuration file.</p>';
    exit;
}

// Re-usable JSON object
$json_object = new Json;

// For debugging
if (DEBUG) {
    error_reporting(E_ALL);
}

// Initialize Twig
require __DIR__ . '/twig_init.php';
