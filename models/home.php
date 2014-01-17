<?php
/*
 * Model for home page view
 */

$json = isset($_get['json']) ? true : false;

// include all data about our locales
include __DIR__ . '/../data/locales.php';

// include view
include __DIR__ . '/../views/home.php';
