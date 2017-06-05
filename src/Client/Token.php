<?php
/**
 * StudentConnect API Client - Token Class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client;

use StudentConnect\API\Client\Auth\HMAC\Settings;
use StudentConnect\API\Client\Exceptions\TokenException;

class Token implements \Serializable, \JsonSerializable {

    private $value  = NULL;
    private $expiry = NULL;

    private $expired = FALSE;

    /**
     * Token constructor.
     *
     * @param \stdClass $obj
     *
     * @throws TokenException
     */
    public function __construct( \stdClass $obj ) {

        $data = $obj->data;

        if( ! isset($data->token) )
            throw new TokenException( "Invalid token data: " . print_r($obj) );

        if( ! isset($data->expires_at) )
            throw new TokenException( "Invalid token data: " . print_r($obj) );

        $this->value  = strval($data->token);
        $this->expiry = $data->expires_at;

        $this->expired = $this->isExpired();

    }

    /**
     * Get token value
     * @return null|string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * Returns expiry time
     * @param string $format [optional] desired format
     *
     * @return null
     */
    public function getExpiry($format='U'){
        
        if( 'U' != $format )
            return date($format, $this->expiry);

        return $this->expiry;

    }

    /**
     * Checks if the token is expired
     * @return bool
     */
    public function isExpired(){

        $tz = new \DateTimeZone(Settings::TIMEZONE);

        $now = new \DateTime('now', $tz);
        $then= new \DateTime('now', $tz);

        //expiry date
        $then->setTimestamp($this->expiry);

        return ( $now >= $then );

    }

    /**
     * @return bool
     * @see Token::isExpired()
     */
    public function isValid(){
        return ! $this->expired;
    }


    /**
     * Get token value
     * @return string
     */
    public function __toString() {
        return $this->value;
    }

    /**
     * Creates a new token object from a string
     * @param $value
     * @param $ttl
     * @return self
     */
    public static function createFromString($value, $ttl=1800){

        $obj = new \stdClass();
        $data= new \stdClass();

        $tz       = new \DateTimeZone(Settings::TIMEZONE);
        $now      = new \DateTime('now', $tz);

        $now->setTimestamp( $now->getTimestamp() + $ttl );

        $data->token         = strval($value);
        $data->expires_at    = $now->getTimestamp();

        $obj->data = $data;

        return new self($obj);

    }

    /**
     * @return string
     */
    public function serialize() {
        return serialize([
            'value'     =>  $this->value,
            'expiry'    =>  $this->getExpiry()
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize( $serialized ) {

        $data = unserialize($serialized);

        if( ! is_array($data) and ! isset($data['value']) and ! isset($data['expiry']) )
            throw new \InvalidArgumentException("Input missing required fields or not an array.");

        $this->value    = strval($data['value']);
        $this->expiry   = intval($data['expiry']);
        $this->expired  = $this->isExpired();

    }

    /**
     * @return string
     */
    public function jsonSerialize() {
        return json_encode([
            'value'     =>  $this->value,
            'expiry'    =>  $this->getExpiry(),
            'expired'   =>  $this->expired
        ], JSON_FORCE_OBJECT);
    }

}