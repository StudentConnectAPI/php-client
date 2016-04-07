<?php
/**
 * StudentConnect - HMAC KeyLoader
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client\Auth\HMAC\Key;

use Acquia\Hmac\KeyLoaderInterface;

class Loader implements KeyLoaderInterface{

    /**
     * Loads key given the ID
     * @param string $key
     *
     * @return Instance
     */
    public function load( $key ) {
        return Instance::getInstance( $key );
    }

}