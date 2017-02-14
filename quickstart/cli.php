#!/usr/bin/env php
<?php
/**
 * StudentConnect API Client - QuickStart CLI
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

date_default_timezone_set('UTC');

require_once (__DIR__ . '/../vendor/autoload.php');

use \StudentConnect\API\Client\Token;
use \StudentConnect\API\Client\Client;


$file    = $argv[0];
$endpoint= isset($argv[2]) ? trim($argv[2]) : NULL;
$command = isset($argv[1]) ? $argv[1] : NULL;

//sanitize endpoint
if( 0 === strpos(strtolower($endpoint), 'http') ); else{
    $endpoint = trim("https://{$endpoint}", '/');
}

switch ($command){

    case 'token':
    case 'authorize': {

        //authorize client

        $key    = isset($argv[3]) ? $argv[3] : NULL;
        $secret = isset($argv[4]) ? $argv[4] : NULL;

        try{

            $client = new Client($endpoint, $key, $secret);

            $client->authorize();

            if( $token = $client->getToken() and ( $value = $token->getValue() ) ){

                if( 'token' == $command )
                    echo $token; //output only the token string
                else{
                    echo ( "\n\n(i) Authorize successful. \n");
                    echo ( "\n\n(i) Token: {$value} \n\n");
                }

            }

        }
        catch (Exception $e){
            echo ( "\n\n(!) Error: " . $e->getMessage() . "\n\n");
        }

        break;

    }

    case 'signinURI': {

        //generate signin uri

        $token = isset($argv[3]) ? $argv[3] : NULL;

        try{

            $token  = Token::createFromString($token);
            $client = new Client($endpoint);

            $client->setToken($token);

            if( $uri = $client->generateSignInURI() ){

                echo ( "\n\n(i) Generated signin uri. \n");
                echo ( "\n\n(i) URI: {$uri}?__tkn={$token} \n\n");

            }

        }
        catch (Exception $e){
            echo ( "\n\n(!) Error: " . $e->getMessage() . "\n\n");
        }

        break;

    }

    case 'profile': {

        //display profile data

        $token = isset($argv[3]) ? $argv[3] : NULL;

        try{

            $token  = Token::createFromString($token);
            $client = new Client($endpoint);

            $client->setToken($token);

            if( $profile = $client->getCurrentProfile() ){

                echo ( "\n\n(i) Profile data received. \n");
                echo ( "Email: {$profile->email} \n");
                echo ( "First name: {$profile->first_name} \n");
                echo ( "Last name: {$profile->last_name} \n");
                echo ( "Gender: {$profile->gender} \n\n");

            }
            else
                echo ( "\n\n(i) No profile associated with token... . \n");

        }
        catch (Exception $e){
            echo ( "\n\n(!) Error: " . $e->getMessage() . "\n\n");
        }

        break;


        break;

    }

    default: {

        //no recognizable command/endpoint

        $message = empty($endpoint) ? "Please enter desired api endpoint" : "Invalid command \"{$command}\"" ;

        echo <<<TEXT

 {$message}... . 

 Commands: 
    
    authorize {endpoint} {key} {secret} : authorize with api key and secret
     
    signinURI {endpoint} {token} : obtain a signin uri for token
     
    profile {endpoint} {token} : show profile associated with token
    
  Examples: 
  
    php {$file} authorize endpoint.api apikey apisecret 
    
    php {$file} signinURI endpoint.api mytoken
    
    php {$file} profile endpoint.api mytoken


TEXT;


    }

}

