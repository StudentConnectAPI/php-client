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

/**Validates expected application token
 * @return bool
 */
function hasValidToken(){

    $expected = getenv('APP_TOKEN');

    //check for X-Token header
    $header = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : NULL;
    $token  = $header ? trim($header) : NULL;

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

function authorizeResponse(){

    $now   = time();
    $token = getenv('APP_TOKEN');

    //token permissions
    $permissions = [

        'GET/'              => '*',
        'GET/token'         => '*',

        'GET/client'        => [
            'username',
            'organization',
            'logo',
            'domain',
            'labels'
        ],

        'GET/signin'        => '*',

        'GET/profile'       => [
            'email',
            'connect_id',
            'avatar',
            'first_name',
            'last_name',
            'birthdate',
            'country',
            'language',
            'address'
        ],

        'POST/signin'           => '*',
        'POST/tokens'           => '*',
        'POST/authorize'        => '*',

        'GET/client/meta'       => [
            'ui',
            'endpoints'
        ],

        'GET/institutions'      => [
            'entity_id',
            'logo',
            'name',
            'web_domain'
        ],

        'GET/profile/meta'      => [
            'emails',
            'phones'
        ],

        'GET/institutions/meta'     => [
            'logos',
            'names'
        ],

        'GET/profile/affiliations' => [
            'affiliation',
            'course',
            'graduation_year'
        ]
    ];

    //response data
    $data = [
        'token'         => $token,
        'permissions'   => $permissions,
        'is_ephemeral'  => TRUE,
        'created_at'    => $now,
        'expires_at'    => ( $now+ 86400 ),
    ];

    header( $_SERVER['SERVER_PROTOCOL'] . ' 201 Created', TRUE);
    header('Content-Type: application/json');

    echo json_encode([
        'code'      => 201,
        'status'    => 'success',
        'data'      => $data,
        'meta'      => [ 'cached' => false ]
    ]);

    exit();
}

function signinResponse(){

    $data = [
        'endpoint'   => 'https://signup.endpoint',
        'method'     => 'default',
        'uri'        => 'https://signup.endpoint/launch',
        'with_token' => FALSE
    ];

    header( $_SERVER['SERVER_PROTOCOL'] . ' 200 OK', TRUE);
    header('Content-Type: application/json');

    echo json_encode([
        'code'      => 200,
        'status'    => 'success',
        'data'      => $data,
        'meta'      => [ 'cached' => false ]
    ]);

    exit();

}

function profileResponse(){

    $data = [
        '_id'           => rand(19000, 234000),
        'email'         => 'someone@email.com',
        'first_name'    => 'Sabrina',
        'last_name'     => 'Doe',
        'gender'        => 'female',
        'birthdate'     => '1998-04-14',
        'country'       => 'AS',
        'language'      => 'en',
        'interests'     => ['entertainment', 'technology', 'facebook'],
        'devices'       => ['Macbook', 'iPhone 6'],
        'is_anonymous'  => FALSE
    ];

    header( $_SERVER['SERVER_PROTOCOL'] . ' 200 OK', TRUE);
    header('Content-Type: application/json');

    echo json_encode([
        'code'      => 200,
        'status'    => 'success',
        'data'      => $data,
        'meta'      => [ 'cached' => false ]
    ]);

    exit();

}

function appDataResponse( $data=[] ){

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

    $data = array_merge($default, $data);

    header( $_SERVER['SERVER_PROTOCOL'] . ' 200 OK', TRUE);
    header('Content-Type: application/json');

    echo json_encode([
        'code'      => 200,
        'status'    => 'success',
        'data'      => $data,
        'meta'      => [ 'cached' => false ]
    ]);

    exit();
}

function appDataPatchResponse(){
    //TODO add data patch test
}