<?php
/**
 * StudentConnect API Client - Tests localhost server index page
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

//auto-loader
require_once ( __DIR__ . '/../../../vendor/autoload.php' );
require_once ( __DIR__ . '/include/responses.php' );

$path = trim($_SERVER['REQUEST_URI'], '/');

//return response depending on path
switch ($path){

    case 'authorize': {

        if( isValidSignature() )
            authorizeResponse();
        else
            badRequestResponse('Invalid signature. Please try again.');

    }; break;

    case 'signin': {

        if( hasValidToken() )
            signinResponse();
        else
            badRequestResponse('Invalid token. Please try again.');

    }; break;

    case 'profile': {

        if( hasValidToken() )
            profileResponse();
        else
            badRequestResponse('Invalid token. Please try again.');

    }; break;

    case 'profile/meta/appdata': {
        //TODO test update cap
    }

    default:
        //not found
        header( $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', TRUE);

}