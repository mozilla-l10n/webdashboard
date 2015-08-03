<?php
namespace Webdashboard;

require_once __DIR__ . '/../app/config/init.php';

$view = 'home';
$body_class = '';

// Get query parameters
$json = Utils::getQueryParam('json', false);
$locale = Utils::getQueryParam('locale');
$project = Utils::getQueryParam('project');
$rss = Utils::getQueryParam('rss', false);

if ($project != '') {
    $view = 'project';
} elseif ($locale != '') {
    $view = 'locale';
}

include __DIR__ . '/../app/models/' . $view . '.php';
