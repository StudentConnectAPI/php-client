<?php
/**
 * StudentConnect API Client - Client Settings Class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client;

use StudentConnect\API\Client\Auth\HMAC\Settings;

class Configuration extends Settings{

    public function __construct($endpoint, $key=NULL, $secret=NULL) {

        if( $key )
            $this->setKey($key);

        if( $secret )
            $this->setSecret($secret);

        $this->setEndpoint($endpoint);

    }

}