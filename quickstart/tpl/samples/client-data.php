<?php include '../../include/boostrap.php';

//our client object previously created
global $Client;

use StudentConnect\API\Client\Exceptions\ClientException;

try{

    $data = $Client->get('/client');

    //list response
    var_dump($data);

}
catch (ClientException $e){

    //something broke
    die("Ops! We couldn't get the client's data because: " . $e->getMessage());

}