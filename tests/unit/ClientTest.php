<?php
/**
 * StudentConnect API Client - API Client Test
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

use \Settings;
use StudentConnect\API\Client\Client;

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

    private $canGetProfileMeta        = FALSE;
    private $canPatchProfileMeta      = FALSE;
    private $canCreatePaymentRequests = FALSE;
    private $canListPaymentsRequests  = FALSE;
    private $canDeletePaymentsRequests= FALSE;

    public function setUp() {

        $api_endpoint = getenv('API_ENDPOINT');
        $app_key      = getenv('APP_KEY');
        $app_secret   = getenv('APP_SECRET');

        if( empty($api_endpoint) )
            //invalid api endpoint
            throw new \InvalidArgumentException("Missing API_ENDPOINT env variable.");

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

    public function testGetProfile(){

        $profile = static::$client->getCurrentProfile();

        $this->assertTrue( is_object( $profile ) );

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

                $this->assertObjectHasAttribute('_id', $profile);
                $this->assertObjectHasAttribute('first_name', $profile);
                $this->assertObjectHasAttribute('last_name', $profile);
                $this->assertObjectHasAttribute('birthdate', $profile);
                $this->assertObjectHasAttribute('gender', $profile);
                $this->assertObjectHasAttribute('email', $profile);

            }

        }
        else{

            //maybe using remote endpoint

            $this->console->writeln(PHP_EOL);
            $this->console->writeln("Could not get profile data. Probably you're using a remote endpoint and you haven't signed up yet... .");

        }

    }

    public function testGetClient(){

        $client = static::$client->get('/client');

        $this->assertNotEmpty( $client );
        $this->assertTrue( is_object( $client ) );

        $this->assertObjectHasAttribute('_id', $client);
        $this->assertObjectHasAttribute('organization', $client);
        $this->assertObjectHasAttribute('permissions', $client);

    }

    public function testTokenPermissions(){

        $token = static::$client->get('token');

        $this->assertNotEmpty( $token );
        $this->assertTrue( is_object( $token ) );

        $this->assertObjectHasAttribute('permissions', $token);

        if( $permissions = $token->permissions )
            foreach ($permissions as $path=>$fields){

                $method = trim( substr($path, 0, strpos($path, '/') ) );
                $path   = str_replace($method, '', $path);

                if( $path == '/profile/meta' and 'GET' == $method )
                    $this->canGetProfileMeta = TRUE;

                if( $path == '/profile/meta' and 'PATCH' == $method )
                    $this->canPatchProfileMeta = TRUE;

                if( $path == '/profile/payments/requests' and 'POST' == $method )
                    $this->canCreatePaymentRequests = TRUE;

                if( $path == '/profile/payments/requests' and 'GET' == $method )
                    $this->canListPaymentsRequests = TRUE;

                if( $path == '/profile/payments/requests' and 'DELETE' == $method )
                    $this->canDeletePaymentsRequests = TRUE;

            }

        //test allowed permissions one by one

        if( $this->canPatchProfileMeta ){

            $meta = [ 'contact_email' => Settings::profileMetaContactEmail ];

            $profile = self::$client->patch('/profile/meta', $meta);

            $this->assertTrue(is_object($profile));

            $this->assertObjectHasAttribute('_id', $profile);
            $this->assertObjectHasAttribute('email', $profile);

        }

        if( $this->canGetProfileMeta ){

            $meta = self::$client->get('/profile/meta');

            $this->assertTrue( is_object($meta) );
            $this->assertObjectHasAttribute('contact_email', $meta);
            $this->assertTrue(Settings::profileMetaContactEmail == $meta->contact_email, "Profile metadata contact email does not match expected string.");

        }

        if( $this->canCreatePaymentRequests ){

            $request = self::$client->post('/profile/payments/requests', [
                'amount' => Settings::paymentRequestAmount
            ]);

            $this->assertTrue( is_object($request) );
            $this->assertObjectHasAttribute( 'currency', $request );
            $this->assertObjectHasAttribute( 'payprofile_id', $request );
            $this->assertObjectHasAttribute( 'user_id', $request );
            $this->assertObjectHasAttribute( 'amount', $request );

            $this->assertTrue(( Settings::paymentRequestAmount == $request->amount ), "Payment request created with wrong amount!");

        }

        if( $this->canListPaymentsRequests ){

            $requests = self::$client->get('/profile/payments/requests');
            $request  = isset($requests[0]) ? $requests[0] : NULL;

            $this->assertTrue( is_object($request) );
            $this->assertObjectHasAttribute( 'currency', $request );
            $this->assertObjectHasAttribute( 'payprofile_id', $request );
            $this->assertObjectHasAttribute( 'user_id', $request );
            $this->assertObjectHasAttribute( 'amount', $request );

        }

        if( $this->canDeletePaymentsRequests ){

            $requestId = $request->_id;

            self::$client->delete( '/profile/payments/requests/' . $requestId );

        }

    }

}