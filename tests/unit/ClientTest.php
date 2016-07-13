<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

use Codeception\Lib\Console\Output;
use StudentConnect\API\Client\Client;
use \StudentConnect\API\Client\Token;

class ClientTest extends Codeception\TestCase\Test{

    /**
     * @var Client|null
     */
    protected static $client     = NULL;

    /**
     * @var bool
     */
    protected static $hasProfile = FALSE;

    /**
     * @var \Codeception\Lib\Console\Output
     */
    protected $console    = NULL;

    protected function __wait( $seconds ){

        //TODO...

        $delay = ceil($seconds/3);
        $this->console->writeln("Waiting {$delay} seconds ... ");
        sleep( $delay );

        $delay = ceil($seconds/2 - $delay);
        $this->console->writeln("Waiting {$delay} seconds ... ");
        sleep( $delay );


    }

    public function setUp() {

        $api_endpoint = getenv('API_ENDPOINT');
        $app_key      = getenv('APP_KEY');
        $app_secret   = getenv('APP_SECRET');

        if( empty( static::$client ) ){

            //create the client
            static::$client = new Client($api_endpoint, $app_key, $app_secret);

        }

        //setup console
        if( empty($this->console) )
            $this->console = new \Codeception\Lib\Console\Output([]);

        return parent::setUp();

    }

    public function testAuthorization(){

        static::$client->authorize();

        $token = static::$client->getToken();

        $this->assertNotEmpty( $token->getValue() );

    }

    public function testGetSignInURI(){

        $uri = static::$client->generateSignInURI();

        $this->assertNotEmpty($uri);

        if( 'ON' != getenv('API_MOCK') ){

            //using remote API endpoint

            $delay = ( $d = getenv('SIGNIN_DELAY') ) ? intval($d) : 120;

            $this->console->writeln(PHP_EOL);

            $this->console->writeln("Using remote API endpoint... ");
            $this->console->writeln("Generated sign in URI: " . self::$client->tokenizeURI($uri) );
            $this->console->writeln("Follow the URI and grant client access, then come back... ");

            $this->console->writeln("Waiting {$delay} seconds ... ");

            //wait
            sleep( $delay );

            $this->console->writeln('');
        }

    }

    public function testGetProfileData(){

        $profile = static::$client->getCurrentProfile();

        if( $profile ){

            //do we have the anon field
            $this->assertObjectHasAttribute('is_anonymous', $profile);

            self::$hasProfile = TRUE;

            if( $profile->is_anonymous ){
                //profile is anonymous
                $this->assertObjectHasAttribute('email', $profile);
            }
            else{

                //check for profile data

                $this->assertNotEmpty( $profile );

                $this->assertObjectHasAttribute('first_name', $profile);
                $this->assertObjectHasAttribute('last_name', $profile);
                $this->assertObjectHasAttribute('birthdate', $profile);
                $this->assertObjectHasAttribute('email', $profile);
                $this->assertObjectHasAttribute('interests', $profile);
                $this->assertObjectHasAttribute('devices', $profile);

            }

        }
        else{

            //maybe using remote endpoint

            $this->console->writeln(PHP_EOL);
            $this->console->writeln("Could not get profile data. Probably you're using a remote endpoint and you haven't signed up yet... .");

        }

    }

}