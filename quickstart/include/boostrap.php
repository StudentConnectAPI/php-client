<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

require_once ( __DIR__ . '/../../vendor/autoload.php' );
require_once ( __DIR__ .'/helpers.php');

//set session defaults
ini_set('session.cookie_lifetime', 7200);
ini_set('session.gc_maxlifetime', 7200);

//start the current session
session_name("STUDENTCONNECTCLIENT");
session_start();

if( isset($_GET['logout']) and boolval($_GET['logout']) )
    dropOptions();

if( count($_POST) ){

    if( isset($_POST['api_endpoint']) and strval($_POST['api_endpoint']) )
        setOption('api_endpoint', $_POST['api_endpoint']);

    if( isset($_POST['app_key']) and strval($_POST['app_key']) )
        setOption('app_key', $_POST['app_key']);

    if( isset($_POST['app_secret']) and strval($_POST['app_secret']) )
        setOption('app_secret', $_POST['app_secret']);

}

define('API_ENDPOINT'   , getOption('api_endpoint'));
define('APP_KEY'        , getOption('app_key'));
define('APP_SECRET'     , getOption('app_secret'));

//globals
$Client = NULL;
$Error  = NULL;