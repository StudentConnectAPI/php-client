<?php
/**
 * StudentConnect API Client - Client Settings Class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client;

use StudentConnect\API\Client\Auth\HMAC\Settings;

class Configuration extends Settings{

    protected $requestTimeout = 7;

    /**
     * Configuration constructor.
     *
     * @param $endpoint : Endpoint URI
     * @param string $key : App key
     * @param string $secret : App secret
     */
    public function __construct($endpoint, $key=NULL, $secret=NULL) {

        if( $key )
            $this->setKey($key);

        if( $secret )
            $this->setSecret($secret);

        $this->setEndpoint($endpoint);

    }

    /**
     * Set request timeout option
     * @param $seconds
     */
    public function setRequestTimeout($seconds){
        $this->requestTimeout = intval($seconds);
    }

    /**
     * Get req timeout
     * @return int
     */
    public function getRequestTimeout(){
        return $this->requestTimeout;
    }

}