<?php
/**
 * StudentConnect - API Auth HMAC Settings definitions
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.1
 */

namespace StudentConnect\API\Client\Auth\HMAC;

use StudentConnect\API\Client\Auth\HMAC\Exceptions\KeyException;

class Settings{

    /**
     * HMAC Provider String
     */
    const PROVIDER = 'SCAUTH:HMAC';

    /**
     * API Timezone
     */
    const TIMEZONE         = 'UTC';

    /**
     * Timestamp format
     */
    const TIMESTAMP_FORMAT = 'D, d M Y H:i:s \G\M\T';

    /**
     * Fixed length for app key
     */
    const KEY_LENGTH    = 32;

    /**
     * Fixed length for app secret
     */
    const SECRET_LENGTH = 42;

    /**
     * API Endpoint
     * @var string
     */
    protected $endpoint  = 'http://localhost';

    /**
     * Application secret
     * @var string|null
     */
    protected $secret = NULL;

    /**
     * Application key
     * @var string|null
     */
    protected $key = NULL;

    public function getEndpoint(){
        return $this->endpoint;
    }

    protected function setEndpoint( $endpoint ){
        $this->endpoint = $endpoint;
    }

    /**
     * @return null|string
     */
    public function getSecret(){
        return $this->secret;
    }

    /**
     * Set secret
     * @param $secret
     *
     * @throws KeyException
     */
    protected function setSecret( $secret ){

        if( strlen($secret) == self::SECRET_LENGTH )
            $this->secret = $secret;
        else
            throw new KeyException("Invalid app secret. Please check your settings and try again");

    }

    /**
     * @return null|string
     */
    public function getKey(){
        return $this->key;
    }

    /**
     * Set key
     * @param $key
     *
     * @throws KeyException
     */
    protected function setKey( $key ){

        if( strlen( $key ) == self::KEY_LENGTH )
            $this->key = $key;
        else
            throw new KeyException("Invalid app key. Please check your settings and try again.");

    }
}