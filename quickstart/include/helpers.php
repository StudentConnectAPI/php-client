<?php
/**
 * StudentConnect API Client - Tests Quickstart Helpers
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */


function getOption($key){

    if( ! isset( $_SESSION ) or ! $_SESSION )
        return FALSE;

    return ( isset($_SESSION[$key]) ? $_SESSION[$key] : NULL );
}


function setOption($key, $value=1){
    $_SESSION[$key] = $value;
}

function dropOptions(){
    unset($_SESSION); session_destroy();
}

function display($name, $force=FALSE){

    global $Client, $Error;

    if( $Error and ! $force )
        //an error has already been reported
        return;

    $filename = realpath( __DIR__ . '/../tpl/' . basename($name) . '.php' );

    if ( $filename and file_exists($filename) )

        try{
            return include $filename;
        }
        catch(\StudentConnect\API\Client\Exceptions\ClientException $e){

            show_error( $e->getMessage() );

        }
        catch(\Exception $e){

            show_error( $e->getMessage() );
        }

    else
        show_error("Could not find template {$name}.");

}

function code_snippet($snippet, $vars=[], $cutLines=1){

    $filename = realpath( __DIR__ . '/../tpl/samples/' . basename($snippet) . '.php' );

    if( is_file($filename) and is_readable($filename) ){

        $contents = file_get_contents($filename);

        foreach ($vars as $key=>$value)
            $contents = str_replace("{{{$key}}}", $value, $contents);

        if( $cutLines )
            for ($i=0; $i<$cutLines; $i++)
                $contents= substr($contents, strpos($contents, "\n")+1);

        return ('<pre><code class="php">' . htmlentities( "<?php\n\n" . trim($contents) ) . '</code></pre>');

    }

    return NULL;

}

function show_error($message, $request=NULL, $response=NULL){

    global $Error, $Client;

    $Error = TRUE;

    ?>
    <div class="alert alert-danger">
        <p><i class="fa fa-warning"></i> <span class="text"><?php echo $message; ?></span> </p>
        <?php if( ! $Client->hasToken() ): ?>
            <p>Would you like to <a href="?logout=1">correct your credentials</a> ?</p>
        <?php endif; ?>
    </div>
    <hr style="clear: both;"/>

    <?php

    include_once ( __DIR__ . '/ui-bootstrap.php' );

    if( ! $Client->hasToken() )
        die(); //fatal error

}

function has_error(){

    global $Error;

    return $Error ? TRUE : FALSE;

}

function init_client(){

    global $Client, $Error;

    if ( ! $Client and API_ENDPOINT ){

        //initialize the client
        try{

            if( $token = getOption('api_token') ){
                $Client = new \StudentConnect\API\Client\Client(API_ENDPOINT);
                $Client->setToken($token);

                if( $Client->getCurrentProfile() )
                    setOption('verified', TRUE);
            }
            else{

                return $Client = new \StudentConnect\API\Client\Client(API_ENDPOINT, APP_KEY, APP_SECRET);
                //could not initialize the client with current credentials
                dropOptions();

            }

        }
        catch(\Exception $e){

            show_error( $e->getMessage() );
        }

    }

    return $Client;

}

function have_client(){

    global $Client;

    return ($Client);

}

function is_verified(){

    global $Client;

    if( $verified = getOption('verified') )
        return $verified;

    return ( $Client and $Client->hasToken() and $Client->getCurrentProfile() );
}