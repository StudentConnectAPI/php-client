<?php include '../../include/boostrap.php'; global $Client;

define('ENDPOINT', '{{endpoint}}');
define('KEY'     , '{{key}}');
define('SECRET'  , '{{secret}}');

use StudentConnect\API\Client\Client;
use StudentConnect\API\Client\Exceptions\ClientException;

try{

    $Client = new Client(ENDPOINT, KEY, SECRET);
    $Client->authorize();

    //get the token object
    $Token = $Client->getToken();

    //we're using PHP's internal APC store, but any session or cache storage will do
    apc_store('api_token', $Token, 86400);

}
catch (ClientException $e){

    //something went wrong
    die("Ops! Client authorization failed: " . $e->getMessage());

}