<?php include '../../include/boostrap.php'; global $Client;

define('ENDPOINT', '{{endpoint}}');
define('KEY'     , '{{key}}');
define('SECRET'  , '{{secret}}');

use StudentConnect\API\Client\Client;
use StudentConnect\API\Client\Exceptions\ClientException;

try{

    //retrieve token saved previously
    $Token = apc_fetch('api_token');

    //initialize client with the token
    $Client = new Client(ENDPOINT);
    $Client->setToken($Token);


}
catch (ClientException $e){

    //something went wrong
    die("Ops! Client authorization failed: " . $e->getMessage());

}