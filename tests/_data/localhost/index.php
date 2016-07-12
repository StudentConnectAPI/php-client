<?php
/**
 * StudentConnect API Client - Tests localhost server index page
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

$path = trim($_SERVER['REQUEST_URI'], '/');


switch ($path){

    case 'authorize': {

        header('Content-Type: application/json');

        echo file_get_contents(__DIR__ . '/responses/authorize.json');

    }; break;


    case 'token': {

    }; break;

    case 'profile': {

    }; break;

    default:
        //not found
        header( $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', TRUE);

}