<?php

use \StudentConnect\API\Client\Token;

class TokenTest extends Codeception\TestCase\Test {

    // tests
    public function testCreationFromString() {

        //set default timezone
        $default = date_default_timezone_get();
        date_default_timezone_set( \StudentConnect\API\Client\Auth\HMAC\Settings::TIMEZONE );

        $pseudorandom = str_shuffle('abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVZYZ0123456789');
        $ttl          = 7200;
        $expiry       = time()+$ttl;

        $ourToken     = Token::createFromString($pseudorandom, $ttl);

        $this->assertTrue( is_object($ourToken) );
        $this->assertTrue( $ourToken->isValid() );
        $this->assertEquals( $pseudorandom, $ourToken->getValue() );
        $this->assertTrue( $ourToken->getExpiry() == $expiry );

        date_default_timezone_set($default);

    }
    
}
