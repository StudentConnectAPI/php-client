<?php

use \StudentConnect\API\Client\Token;

class TokenTest extends \PHPUnit_Framework_TestCase {

    protected function setUp() {
        //set up
    }

    protected function tearDown() {
        //tear down
    }

    // tests
    public function testCreationFromString() {

        $pseudorandom = str_shuffle('abcdefghijklmnopqrstuvxyz0123456789');
        $ourToken     = Token::createFromString($pseudorandom);

        $this->assertTrue(is_object($ourToken));
        $this->assertTrue($ourToken->isValid());
        $this->assertEquals($pseudorandom, $ourToken->getValue());

    }
    
}
