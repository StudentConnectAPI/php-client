<?php
// This is global bootstrap for autoloading

//auto-loader
require_once (__DIR__ . '/../vendor/autoload.php');

//setup server host
$_SERVER['HTTP_HOST'] = 'localhost';