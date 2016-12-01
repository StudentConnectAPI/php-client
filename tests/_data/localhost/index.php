<?php
/**
 * StudentConnect API Client - Tests localhost server index page
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

//auto-loader
require_once ( __DIR__ . '/../../../vendor/autoload.php' );
require_once ( __DIR__ . '/include/responses.php' );

$data   = [];
$method = $_SERVER['REQUEST_METHOD'];
$path   = trim($_SERVER['REQUEST_URI'], '/');

if( in_array($method, ['POST', 'PATCH']) )
    $data = json_decode( @file_get_contents('php://input'), TRUE );

//return response depending on path
switch ($path){

    //authorize
    case 'authorize': {

        if( isValidSignature() )
            authorizeResponse();
        else
            badRequestResponse('Invalid signature. Please try again.');

    }; break;

    //signin
    case 'signin': {

        if( hasValidToken() )
            signinResponse();
        else
            badRequestResponse('Invalid token. Please try again.');

    }; break;

    //profile
    case 'profile': {

        if( hasValidToken() )
            profileResponse();
        else
            badRequestResponse('Invalid token. Please try again.');

    }; break;

    //token
    case 'token': {

        if( hasValidToken() )
            tokenResponse();
        else
            badRequestResponse('Invalid token. Please try again.');

    }; break;

    //client
    case 'client': {

        if( hasValidToken() )
            clientResponse();
        else
            badRequestResponse('Invalid token. Please try again.');

    }; break;

    //profile/meta
    case 'profile/meta': {
        profileMetaResponse($data);
    }; break;

    //profile/payments/requests
    case 'profile/payments/requests': {
        profilePaymentsRequestsResponse($data, $method);
    }; break;

    case ('profile/payments/requests/' . \Settings::paymentRequestId ) : {
        profilePaymentsRequestsResponse($data, $method);
    }; break;

    default:
        //not found
        header( $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', TRUE);

}