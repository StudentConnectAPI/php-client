<?php include '../../include/boostrap.php';

//our client object previously created
global $Client;

use StudentConnect\API\Client\Client;
use StudentConnect\API\Client\Exceptions\ClientException;

try{

    $uri = $Client->generateSignInURI([
        //we could send here things as email, institution_id, first_name, last_name and more
    ], FALSE);

    echo ( '<a href="'. $uri .'" target="_self">Verify your student account &rarr;</a>' );

}
catch (ClientException $e){

    //something went wrong
    die("Ops! We couldn't generate the signin uri because: " . $e->getMessage());

}