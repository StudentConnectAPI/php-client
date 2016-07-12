<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

require_once ( __DIR__ . '/../../../../vendor/autoload.php' );

class KeyLoader implements \Acquia\Hmac\KeyLoaderInterface{

    public function load( $id ) {

        $key = new Key();

        if( $id == $key->getId() )
            return $key;

        return NULL;

    }

}