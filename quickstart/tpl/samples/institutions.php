<?php include '../../include/boostrap.php';

//our client object previously created
global $Client;

use StudentConnect\API\Client\Client;
use StudentConnect\API\Client\Exceptions\ClientException;

try{

    $institutions = $Client->get('/institutions', [
        //filters
        'country' => 'GB',
        'limit'   => 2
    ]);

    //list response
    var_dump($institutions);

}
catch (ClientException $e){

    //something broke
    die("Ops! We couldn't get the instituions list because: " . $e->getMessage());

}