<?php

require_once __DIR__ . '/../app/config/init.php';

$view = 'home';
$body_class = '';

if (isset($_GET['project'])) {
    $view = 'project';
} elseif (isset($_GET['locale'])) {
    $view = 'locale';
}

include __DIR__ . '/../app/models/' . $view . '.php';
