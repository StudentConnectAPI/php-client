<?php
/**
 * StudentConnect - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */


namespace StudentConnect\API\Client\Auth\HMAC\Key;

use Acquia\Hmac\KeyInterface;
use StudentConnect\API\Client\Auth\HMAC\Settings;
use StudentConnect\API\Client\Auth\HMAC\Exceptions\KeyException;

class Instance implements KeyInterface{

    /**
     * @var Instance
     */
    protected static $instance = NULL;

    /**
     * @var string|null
     */
    protected $id = NULL;

    /**
     * @var string|null
     */
    protected $secret = NULL;

    protected function __construct( $key, Settings $settings=NULL ){

        if( empty($settings) )
            $settings = new Settings();

        if( $key != $settings->getKey() )
            throw new KeyException("Invalid application key {$key}. Please check your key settings and try again.");

    }

    public static function getInstance($key, Settings $settings){

        if( empty( self::$instance ) )
            self::$instance = new Instance($key, $settings);
        else
            if( self::$instance->getId() != $key )
                throw  new KeyException("Could not initialize a new key instance!");

        return self::$instance;

    }

    public function getId(){
        return $this->id;
    }

    public function getSecret() {
        return $this->secret;
    }

}