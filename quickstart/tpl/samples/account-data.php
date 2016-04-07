<?php include '../../include/boostrap.php';

//our client object previously created
global $Client;

use StudentConnect\API\Client\Client;
use StudentConnect\API\Client\Exceptions\ClientException;

try{

    //retrieve user profile
    $profile = $Client->getProfile();

    //see what we've got
    var_dump($profile);

}
catch (ClientException $e){

    //something went wrong
    die("Ops! We couldn't generate the signin uri because: " . $e->getMessage());

}