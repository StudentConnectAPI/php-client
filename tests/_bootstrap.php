<?php
// This is global bootstrap for autoloading

//auto-loader
require_once ( __DIR__ . '/../vendor/autoload.php' );

//setup server host
$_SERVER['HTTP_HOST'] = 'localhost';

define('API_ENDPOINT'   , 'http://localhost:8087');
define('APP_KEY'        , '2016.W4XVJLvpxBv6gipYT0s2f5wHkps');
define('APP_SECRET'     , 'vuKWzDdItol6yPT0gkqSBxu4w6ooch5IeAZPttSnaf');

//setup api endpoint
if( NULL == getenv('API_ENDPOINT') )
    putenv('API_ENDPOINT=' . API_ENDPOINT);

if( NULL == getenv('APP_KEY') )
    putenv('APP_KEY=' . APP_KEY);

if( NULL == getenv('APP_SECRET') )
    putenv('APP_SECRET=' . APP_SECRET);