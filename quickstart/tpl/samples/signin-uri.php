<?php include '../../include/boostrap.php';

//our client object previously created
global $Client;

use StudentConnect\API\Client\Client;
use StudentConnect\API\Client\Exceptions\ClientException;

try{

    $uri = $Client->generateSignInURI();


    //-- output the sign in form --//
    ?>

    <form method="post" target="_self" action="<?php echo $uri; ?>">

        <?php

            //tokenize the form, so we have our token sent with the request
            $Client->tokenizeForm();

        ?>

        <!-- the sign in button -->
        <button type="submit">
            <img src="https://cdn.studentconnectapi.com/branding/buttons/studentconnect-button-blue-sm.png"/>
        </button>
        <!-- the sign in button -->

    </form>

    <?php
    //-- output the sign in form --//

}
catch (ClientException $e){

    //something went wrong
    die("Ops! We couldn't generate the signin uri because: " . $e->getMessage());

}