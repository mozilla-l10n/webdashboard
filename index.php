<?php

require_once __DIR__ . '/config/init.php';
include __DIR__ . '/models/' . (isset($_GET['locale']) ? 'locale' : 'home'). '.php';

