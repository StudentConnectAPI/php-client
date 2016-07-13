<?php
// This is global bootstrap for autoloading

//auto-loader
require_once ( __DIR__ . '/../vendor/autoload.php' );

//setup server host
$_SERVER['HTTP_HOST'] = 'localhost';

define('IS_UNIT_TESTING', TRUE);

//defaults
define('DEFAULT_API_ENDPOINT'   , 'http://localhost:8087');
define('DEFAULT_APP_KEY'        , '2016.mvJ3ftVmt89yMSCf0CRNpdvCC1z');
define('DEFAULT_APP_SECRET'     , 'SiGRXRr7Y9bsCrOAC68fZ9OpsKhFp8KmuKpCLZ7TNT');

define('DEFAULT_APP_TOKEN'      , 'EN5C4EcfVS6kaY0HBjAWu2YahmAeLApKPft0Vb8KkuvaqvMrd');

define('DEFAULT_SIGNIN_DELAY', 128); //in seconds

//setup defaults
if( NULL == getenv('API_ENDPOINT') ){
    putenv('API_MOCK=ON');
    putenv('API_ENDPOINT=' . DEFAULT_API_ENDPOINT);
}
else
    putenv('SIGNIN_DELAY=' . DEFAULT_SIGNIN_DELAY);

if( NULL == getenv('APP_KEY') )
    putenv('APP_KEY=' . DEFAULT_APP_KEY);

if( NULL == getenv('APP_SECRET') )
    putenv('APP_SECRET=' . DEFAULT_APP_SECRET);

if( NULL == getenv('APP_TOKEN') )
    putenv('APP_TOKEN=' . DEFAULT_APP_TOKEN);