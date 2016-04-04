<?php
namespace Webdashboard;

require_once __DIR__ . '/../app/inc/init.php';

// Get query parameters
$json = Utils::getQueryParam('json', false);
$locale = Utils::getQueryParam('locale');
$project = Utils::getQueryParam('project');
$rss = Utils::getQueryParam('rss', false);

if ($project != '') {
    include __DIR__ . '/../app/controllers/project.php';
} elseif ($locale != '') {
    include __DIR__ . '/../app/controllers/locale.php';
} else {
    include __DIR__ . '/../app/controllers/home.php';
}
