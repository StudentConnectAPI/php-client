<?php include '../../include/boostrap.php';

//our client object previously created
global $Client;

use StudentConnect\API\Client\Client;
use StudentConnect\API\Client\Exceptions\ClientException;

try{

    $uri = $Client->generateSignInURI([
        //we could send here things as email, institution_id, first_name, last_name and more
    ], FALSE);


    //-- output the sign in form --//
    ?>

    <form method="post" target="_self" action="<?php echo $uri; ?>">

        <?php

            //tokenize the form, so we have our token sent with the request
            $Client->tokenizeForm();

        ?>

        <!-- the actual button -->
        <button class="btn btn-lg btn-info" type="submit">
            Verify your student account &rarr;</i>
        </button>
        <!-- the actual button -->

    </form>

    <?php
    //-- output the sign in form --//

}
catch (ClientException $e){

    //something went wrong
    die("Ops! We couldn't generate the signin uri because: " . $e->getMessage());

}