<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

use StudentConnect\API\Client\Client;

class ClientTest extends Codeception\TestCase\Test{

    static $api_endpoint;

    static $app_key;
    static $app_secret;

    /**
     * @var Client|null
     */
    static $client = NULL;

    /**
     * @var \Codeception\Extension\PhpBuiltinServer|null
     */
    static $server = NULL;

    public function setUp() {

        self::$api_endpoint = getenv('API_ENDPOINT');
        self::$app_key      = getenv('APP_KEY');
        self::$app_secret   = getenv('APP_SECRET');

        if( empty( self::$client ) )
            //create the client
            self::$client = new StudentConnect\API\Client\Client(self::$api_endpoint, self::$app_key, self::$app_secret);

        return parent::setUp();

    }

    public function testAuthorization(){

        $expected = 'EKyp4mc3RzmhIeS8L8FQ03NPv68gFT5PgvJKigVrNReH1gITa';
        $token    = self::$client->getToken();

        $this->assertTrue( ( $expected == $token->getValue() ), "Token value does not match expected value." );
        
    }

    public function testGetSignInURI(){
        //TODO...
    }

    public function testGetProfileData(){
        //TODO...
    }

}