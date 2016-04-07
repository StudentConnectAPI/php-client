<?php
/**
 * StudentConnect API Client - Token Class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client;

use StudentConnect\API\Client\Auth\HMAC\Settings;
use StudentConnect\API\Client\Exceptions\TokenException;

class Token{

    private $value  = NULL;
    private $expiry = NULL;

    private $expired = FALSE;

    public function __construct(\stdClass $obj ) {

        $data = $obj->data;

        if( ! isset($data->token) )
            throw new TokenException( "Invalid token data: " . print_r($obj) );

        if( ! isset($data->expires_at) )
            throw new TokenException( "Invalid token data: " . print_r($obj) );

        $this->value  = strval($data->token);
        $this->expiry = $data->expires_at;

        $this->expired = $this->isExpired();

    }

    public function getValue(){
        return $this->value;
    }

    public function getExpiry($format='U'){
        
        if( 'U' != $format )
            return date($format, $this->expiry);

        return $this->expiry;
    }

    public function isExpired(){

        $tz = new \DateTimeZone(Settings::TIMEZONE);

        $now = new \DateTime('now', $tz);
        $then= new \DateTime('now', $tz);

        $then->setTimestamp($this->expiry);

        return ( $now >= $then );
    }

    public function isValid(){
        return ! $this->expired;
    }


    /**
     * @return string
     */
    public function __toString() {
        return $this->value;
    }

}