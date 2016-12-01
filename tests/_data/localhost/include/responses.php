<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

require_once ( __DIR__ . '/../../../../vendor/autoload.php' );

require_once ( __DIR__ . '/Key.php' );
require_once ( __DIR__ . '/KeyLoader.php' );

use Acquia\Hmac\RequestAuthenticator;
use \StudentConnect\API\Client\Auth\HMAC\Settings;
use Acquia\Hmac\Request\Symfony as RequestWrapper;
use StudentConnect\API\Client\Auth\HMAC\Request\Signer;

/**
 * Validates expected application token
 * @return bool
 */
function hasValidToken(){

    $expected = getenv('APP_TOKEN');

    //check for Authorization header
    $header = trim( $_SERVER['HTTP_AUTHORIZATION'] );
    $token  = trim( str_replace('TOKEN ', '', $header) );

    if( empty($token) and isset($_SERVER['HTTP_X_TOKEN'])){

        //check for X-Token header
        $header = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : NULL;
        $token  = $header ? trim($header) : NULL;

    }

    return ( $expected == $token ) ? TRUE : FALSE;

}

/**
 * Validates incoming request signature
 * @return bool
 */
function isValidSignature(){

    try{

        $request        = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $requestWrapper = new RequestWrapper($request);

        $keyLoader = new KeyLoader();
        $signer    = new Signer( Settings::PROVIDER );

        $authenticator = new RequestAuthenticator($signer, '+10 minutes');
        $key           = $authenticator->authenticate($requestWrapper, $keyLoader);

        if( $key )
            return TRUE;

        return FALSE;

    }
    catch (\Exception $e){
        badRequestResponse( $e->getMessage() );
    }

}

/**
 * Returns a bad request response
 * @param $message
 */
function badRequestResponse( $message = 'Something went wrong... .' ){

    header( $_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', TRUE);
    header('Content-Type: application/json');

    echo json_encode([
        'code'      => 400,
        'status'    => 'error',
        'message'   => $message,
    ]);

    exit();

}

function __successResponse($data=[], $meta=['cached'=>FALSE], $message='Resource available'){

    header( $_SERVER['SERVER_PROTOCOL'] . ' 200 OK', TRUE);
    header('Content-Type: application/json');

    echo json_encode([
        'code'      => 201,
        'status'    => 'success',
        'message'   => $message,
        'data'      => $data,
        'meta'      => $meta
    ]);

    exit();

}

function __createdResponse($data=[], $meta=['cached'=>FALSE]){

    header( $_SERVER['SERVER_PROTOCOL'] . ' 201 Created', TRUE);
    header('Content-Type: application/json');

    echo json_encode([
        'code'      => 201,
        'status'    => 'success',
        'message'   => 'Resource created',
        'data'      => $data,
        'meta'      => $meta
    ]);

    exit();

}

function __updatedResponse($data=[], $meta=['cached'=>FALSE]){
    __successResponse($data, $meta, 'Resource updated');
}

function __deletedResponse(){

    header( $_SERVER['SERVER_PROTOCOL'] . ' 202 Accepted', TRUE);
    header('Content-Type: application/json');

    echo json_encode([
        'code'      => 202,
        'status'    => 'success',
        'message'   => 'Resource deleted'
    ]);

    exit();

}

/**
 * Mocks the /authorize response
 */
function authorizeResponse(){

    $now   = time();
    $token = getenv('APP_TOKEN');

    //token permissions
    $permissions = [
        'GET/'                      => '*',
        'GET/token'                 => '*',
        'GET/client'                => '*',
        'GET/signin'                => '*',
        'GET/profile'               => '*',
        'POST/signin'               => '*',
        'POST/tokens'               => '*',
        'POST/authorize'            => '*',
        'GET/client/meta'           => '*',
        'GET/institutions'          => '*',
        'GET/profile/meta'          => '*',
        'GET/institutions/meta'     => '*',
        'GET/profile/affiliations'  => '*'
    ];

    __createdResponse([
        'token'         => $token,
        'permissions'   => $permissions,
        'is_ephemeral'  => TRUE,
        'created_at'    => $now,
        'expires_at'    => ( $now+ 86400 ),
    ]);
}

/**
 * Mocks the /signin response
 */
function signinResponse(){

    __createdResponse([
        'endpoint'   => 'https://signup.endpoint',
        'method'     => 'default',
        'uri'        => 'https://signup.endpoint/launch',
        'with_token' => FALSE
    ]);

}

/**
 * Mocks the /profile response
 */
function profileResponse(){

    __successResponse([
        '_id'           => rand(19000, 234000),
        'email'         => 'someone@email.com',
        'first_name'    => 'Sabrina',
        'last_name'     => 'Doe',
        'gender'        => 'female',
        'birthdate'     => '1998-04-14',
        'country'       => 'AS',
        'language'      => 'en',
        'is_anonymous'  => FALSE
    ]);

}

/**
 * Mocks /client response
 */
function clientResponse(){

    __successResponse([
        '_id'           => rand(19000, 234000),
        'username'      => 'client-organization',
        'organization'  => 'Organization Inc.',
        'permissions'   => [
            'GET/'  =>  '*',
            'POST/authorize'  =>  '*',
            'GET/token'  =>  '*',
            'GET/client'  =>  '*',
            'GET/profile'  =>  '*',
            'GET/profile/meta/appdata'  =>  '*',
            'POST/profile/meta/appdata'  =>  '*',
            'PATCH/profile/meta/appdata'  =>  '*'
        ]
    ]);

}

/**
 * Mocks /token response
 */
function tokenResponse(){

    __successResponse([
        '_id'        => rand(19000, 234000),
        'client_id'  => rand(19000, 234000),
        'user_id'    => rand(19000, 234000),
        'value'      => getenv('APP_TOKEN'),
        'granted'    => TRUE,
        'expires_at' => time()+ 86400,
        'created_at' => time(),
        'updated_at' => time(),

        'permissions'=> [
            'GET/'  =>  '*',
            'POST/authorize'  =>  '*',
            'GET/token'  =>  '*',
            'GET/client'  =>  '*',
            'GET/profile'  =>  '*',
            'GET/profile/meta'  =>  '*',
            'PATCH/profile/meta'  =>  '*',
            'POST/profile/payments/requests'  =>  '*',
        ]
    ]);

}

/**
 * Mocks /profile/meta response
 * @param array $data
 */
function profileMetaResponse( $data=[] ){

    $default = [
        'appId' => ( rand(19000, 234000) . str_shuffle('abcdefghij') ),
        'user'  => [
            'login'     => 'app282173@email.com',
            'password'  => 'sha1:2ac9a6746aca543af8dff39894cfe8173afba21eb01c6fae33d52947222855ef',
            'prefs'     => [
                'emoji' => 'yes',
                'color' => 'A43A34'
            ]
        ]
    ];

    if( empty($data) )
        //GET response
        __successResponse( array_merge($default, [ 'contact_email' => \Settings::profileMetaContactEmail ]) );

    else
        //PATCH response
        profileResponse();

}

/**
 * Mocks /profile/payments/requests
 * @param array $data
 * @param $method
 */
function profilePaymentsRequestsResponse( $data=[], $method='GET' ){

    $default = [
        '_id'           => \Settings::paymentRequestId,
        'user_id'       => rand(19000, 234000),
        'payprofile_id' => rand(19000, 234000),
        'amount'        => 17.95,
        'currency'      => 'GBP',
        'locked'        => FALSE,
        'processed'     => FALSE
    ];

    if( empty($data) )
        //GET response
        __successResponse( [$default] );
    else{

        if( 'POST' == $method )
            //POST response
            __createdResponse( array_merge($default, $data) );

        if( 'DELETE' == $method )
            //DELETE response
            __deletedResponse();

    }

}