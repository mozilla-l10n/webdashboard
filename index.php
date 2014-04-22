<?php

require_once __DIR__ . '/config/init.php';

$view = 'home';
$body_class = 'sand';

if (isset($_GET['project'])) {
	$view = 'project';
} elseif (isset($_GET['locale'])) {
	$view = 'locale';
}

include __DIR__ . '/models/' . $view . '.php';
