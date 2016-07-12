<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

require_once ( __DIR__ . '/../../../../vendor/autoload.php' );

class Key implements \Acquia\Hmac\KeyInterface{

    protected $key     = NULL;
    protected $secret = NULL;

    public function __construct() {

        //get key and secret from env vars
        $this->key    = getenv('APP_KEY');
        $this->secret = getenv('APP_SECRET');
    }

    public function getId() {
        return $this->key;
    }

    public function getSecret() {
        return $this->secret;
    }

}