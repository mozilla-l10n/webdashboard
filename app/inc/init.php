<?php
namespace Webdashboard;

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
    echo "<h1>Error: missing config file</h1>";
    echo "<p>Config file is missing: <code>{$settings_filename}</code></p>";
    echo "<p>Rename <strong>app/config/settings.inc.php.ini</strong> as <strong>app/config/settings.inc.php</strong> to get started.</p>";
    exit;
}

// Include data about all supported locales
include __DIR__ . '/../data/locales.php';

// Re-usable JSON object
$json_object = new Json;

// Cache class
define('CACHE_ENABLED', true);
define('CACHE_PATH', __DIR__ . '/../../cache/');   // This folder needs to be writable by PHP
define('CACHE_TIME', 15 * 60);    // Default: 15 minutes

// For debugging
if (DEBUG) {
    error_reporting(E_ALL);
}

// Initialize Twig
require __DIR__ . '/twig_init.php';
