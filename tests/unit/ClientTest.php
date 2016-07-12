<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

use StudentConnect\API\Client\Client;

class ClientTest extends Codeception\TestCase\Test{

    /**
     * @var Client|null
     */
    protected static $client = NULL;

    /**
     * @var \StudentConnect\API\Client\Token|null
     */
    protected static $token = NULL;

    public function setUp() {

        $api_endpoint = getenv('API_ENDPOINT');
        $app_key      = getenv('APP_KEY');
        $app_secret   = getenv('APP_SECRET');

        if( empty( static::$client ) ){

            //create the client
            static::$client = new Client($api_endpoint, $app_key, $app_secret);


        }

        if( static::$token )
            static::$client->setToken( static::$token );

        return parent::setUp();

    }

    public function testAuthorization(){

        static::$client->authorize();

        $token = static::$client->getToken();

        $this->assertNotEmpty( $token->getValue() );

        static::$token = $token;

    }

    public function testGetSignInURI(){

        $uri = static::$client->generateSignInURI();

        $this->assertNotEmpty($uri);

    }

    public function testGetProfileData(){

        $profile = static::$client->getCurrentProfile();

        $this->assertNotEmpty($profile);

        $this->assertObjectHasAttribute('first_name', $profile);
        $this->assertObjectHasAttribute('last_name', $profile);
        $this->assertObjectHasAttribute('birthdate', $profile);
        $this->assertObjectHasAttribute('email', $profile);

        $this->assertObjectHasAttribute('is_anonymous', $profile);

    }

}