<?php
/**
 * StudentConnect API Client - Client Class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Exception\ServerException;
use StudentConnect\API\Client\Auth\HMAC\Headers;
use \StudentConnect\API\Client\Auth\HMAC\Settings;
use StudentConnect\API\Client\Auth\HMAC\Middleware;
use StudentConnect\API\Client\Auth\HMAC\Request\Signer;
use StudentConnect\API\Client\Exceptions\TokenException;
use \StudentConnect\API\Client\Exceptions\ClientException;
use StudentConnect\API\Client\Exceptions\ResourceNotFoundException;
use StudentConnect\API\Client\Exceptions\ServiceUnavailableException;

class Client{

    const VERSION = '0.2.3';

    const GET     = 'GET';
    const POST    = 'POST';
    const PUT     = 'PUT';
    const PATCH   = 'PATCH';
    const DELETE  = 'DELETE';

    const SUCCESS = 'success';
    const ERROR   = 'error';

    /**
     * default client headers
     * @var array
     */
    protected $headers = [
        'User-Agent'        => ( 'StudentConnect API Client v' . self::VERSION ),
        'Accept'            => 'application/json; charset=UTF-8',
    ];

    /**
     * @var Configuration|null
     */
    protected $cfg  = NULL;

    /**
     * debug setting
     * @var bool
     */
    protected $debug = FALSE;

    /**
     * @var Token|null
     */
    protected $token = NULL;

    /**
     * @var HTTPClient|null
     */
    protected $HTTPClient = NULL;

    /**
     * Last response data
     * @var \stdClass|null
     */
    protected $data = [];

    /**
     * Last response metatada
     * @var \stdClass|null
     */
    protected $meta;

    /**
     * Last response body
     * @var string
     */
    protected $rawResponse;

    /**
     * Last request details
     * @var array
     */
    protected $lastRequest = [];

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Creates new Client instance.
     *
     * @param $endpoint
     * @param $key
     * @param $secret
     */
    public function __construct($endpoint, $key=NULL, $secret=NULL) {
        $this->configure($endpoint, $key, $secret);
    }

    /**
     * Configures client
     * @param $endpoint
     * @param $key
     * @param $secret
     */
    public function configure($endpoint, $key, $secret){
        $this->cfg = new Configuration($endpoint, $key, $secret);
    }

    /**
     * Generates full URI for a resource
     * @param $resource
     *
     * @return mixed
     */
    protected function uri($resource){
        return trim( trim($this->cfg->getEndpoint(), '/') . '/' . ltrim($resource, '/'), '/');
    }

    /**
     * Adds header to client
     * @param string $name The header name
     * @param string $value The header value
     *
     * @return array
     * @throws ClientException
     */
    protected function addHeader($name, $value){

        if( $this->HTTPClient )
            throw new ClientException("Could not add extra headers. HTTP Client had already been initialized.");

        if( ! is_string( $value ) )
            throw new ClientException("Header value should be a string.");

        $this->headers = array_merge($this->headers, [
            $name => $value
        ]);

        return $this->headers;

    }

    /**
     * Builds the http client
     * @return HTTPClient|null
     * @throws ClientException
     */
    protected function http(){

        if( empty($this->HTTPClient) ){

            //default options
            $options = [
                'headers' => &$this->headers,
                'timeout' => 7,
            ];

            if ( $this->token ){

                //setup to send the token with each request
                $this->addHeader(Headers::TOKEN, $this->token->getValue());

            }
            else{

                //setup client to send signed requests
                $signer     = new Signer(Settings::PROVIDER);
                $middleware = new Middleware($signer, $this->cfg->getKey(), $this->cfg->getSecret());

                $stack = HandlerStack::create();
                $stack->push($middleware);

                $options['handler'] = $stack;

            }

            //create the client
            $this->HTTPClient = new HTTPClient($options);

            return $this->HTTPClient;

        }

        return $this->HTTPClient;

    }

    /**
     * Makes a query to the API
     * @param string $resource the resource path
     * @param array $data the query data
     * @param string $method HTTP verb
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws ClientException
     * @throws ResourceNotFoundException
     * @throws ServiceUnavailableException
     * @throws \Exception
     */
    protected function query($resource, $data=[], $method=self::GET){

        $url = $this->uri($resource);

        //save last request data
        $this->lastRequest = [
            'verb'     => $method,
            'resource' => $resource,
            'data'     => count($data) ? $data : []
        ];

        try{

            switch ($method){
                case self::GET:
                    return $this->http()->get($url . ( count($data) ? ( '?' . http_build_query($data) ) : '' ) );

                case self::POST:
                    return $this->http()->post($url, [
                        'form_params' => $data
                    ]);

                case self::PUT:
                    throw new \Exception("Method not supported."); //not yet implemented

                case self::PATCH:
                    throw new \Exception("Method not supported."); //not yet implemented

                case self::DELETE:
                    throw new \Exception("Method not supported"); //not yet implemented

                default:
                    throw new ClientException("Invalid request method: $method.");
            }

        }
        catch (\GuzzleHttp\Exception\ClientException $e){

            //the error is on the client side

            $this->rawResponse = $e->getResponse()->getBody()->__toString();

            switch ( $e->getCode() ){

                case 400:
                    throw new ClientException( $e->getMessage(), $e );

                case 404:
                    throw new ResourceNotFoundException($e->getMessage(), $e);

            }

        }
        catch (ServerException $e){

            //the error is on the API side

            $this->rawResponse = $e->getResponse()->getBody()->__toString();

            throw new ServiceUnavailableException( $e->getMessage(), $e );

        }

    }

    protected function asObj($resource, $method=self::GET, $data=[]){

        $json = $this->asRaw($resource, $method, $data);

        if( empty($json) )
            throw new ClientException("Empty response from the API for resource {$resource}.");

        $obj = @json_decode( $json );

        if( $json and $obj and isset($obj->status) ){

            if( self::SUCCESS == $obj->status ){

                //successful request

                $this->data = isset($obj->data) ? $obj->data : new \stdClass();
                $this->meta = isset($obj->meta) ? $obj->meta : new \stdClass();

                return $obj;

            }
            else
                throw new ClientException("{$obj->code} Error: {$obj->message}");
        }

        throw new ClientException("Invalid response from the API for resource {$resource}.");

    }

    protected function asRaw($resource, $method=self::GET, $data=[]){

        $this->rawResponse = $this->query($resource, $data, $method)->getBody()->__toString();

        return $this->rawResponse;

    }

    /**
     * Returns a pretty-print formatted version of the last response
     * @param string $before
     * @param string $after
     *
     * @return string
     */
    public function getFormattedResponse($before='', $after=''){

        if( $this->rawResponse )
            $formatted = @json_encode( @json_decode($this->rawResponse), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES );
        else
            $formatted = '{}';

        return ($before . $formatted . $after );

    }

    /**
     * Pretty-printed version of the last request
     * @param string $before
     * @param string $after
     *
     * @return string
     */
    public function getFormattedRequest($before='', $after=''){

        $formatted = ( ( $this->lastRequest['verb'] . ' ' . $this->lastRequest['resource'] ) . PHP_EOL );

        if( $this->lastRequest['data'] ){
            $formatted.= PHP_EOL;
            $formatted.= @json_encode($this->lastRequest['data'], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES );
        }

        return ($before . $formatted . $after);

    }

    public function getLastRequestDetails(){
        return $this->lastRequest;
    }

    public function retrieveToken(){

        $this->token = $this->getToken();

        return $this->token;
    }

    /**
     * Sets token to use for api calls
     * @param Token $token
     *
     * @throws TokenException
     */
    public function setToken(Token $token){

        $this->token = $token;

        if( $this->token->isExpired() ) 
            throw new TokenException("Could not initialize the client with expired token. Please generate a new token and try again.");

    }

    /**
     * Obtain the authorization token
     * @return null|Token
     * @throws ClientException
     */
    public function getToken(){

        if( $this->hasToken() )
            return $this->token;

        $token = $this->asObj('/authorize', self::POST);

        if( $token and isset($this->data) )
            return new Token($token);

        throw new ClientException("Could not retrieve token. Please check your credentials and try again.");

    }

    /**
     * Checks current token and it's validity
     * @return bool
     */
    public function hasToken(){
        return ( $this->token and $this->token->isValid() );
    }

    /**
     * Retrieves the authorization token
     */
    public function authorize(){
        $this->retrieveToken();
    }

    public function getResponseData($key=NULL, $default=NULL){
        return $key ? ( isset($this->data->$key) ? $this->data->$key : $default ) : $this->data;
    }

    public function getResponseMeta($key, $default=NULL){
        return $key ? ( isset($this->meta->$key) ? $this->meta->$key : $default ) : $this->meta;
    }

    /**
     * Retrieves a list of institutions
     * @param array $filters
     * @param int $limit
     * @param int $offset
     *
     * @return null
     * @throws ClientException
     */
    public function getInstitutions($filters=[], $limit=15, $offset=0){

        $list = $this->asObj('/institutions', self::GET, array_merge($filters, [
            'limit'  => $limit,
            'offset' => $offset,
        ]));

        return $list->data;

    }

    /**
     * Retrieves current profile associated with the token
     * @return object|null
     * @throws ClientException
     */
    public function getProfile(){

        try{

            if( $this->hasToken() ){

                $account = $this->asObj( '/profiles/' . $this->token->getValue() );

                return $account->data;

            }
            else
                throw new ClientException("You need an authorization token, in order to request a profile.");

        }
        catch (ResourceNotFoundException $e){
            //account does not exist
            return NULL;
        }

    }

    /**
     * Generates the sign in URI according to the data provided
     * @param array $data
     * @param bool $forward
     *
     * @return null
     * @throws ClientException
     */
    public function generateSignInURI($data=[], $forward=FALSE){

        $signIn = $this->asObj('/signin', self::POST, array_merge($data, [
            'forward' => intval($forward)
        ]));

        if( $signIn )
            return $signIn->data->uri;

        return NULL;

    }

    /**
     * Forwards the current request to the api sign in portal
     * @param array $data
     * @param bool $avoidMultiRedirects
     *
     * @return mixed
     * @throws ClientException
     */
    public function forwardSignInRequest($data=[], $avoidMultiRedirects=FALSE){

        if( headers_sent() )
            throw new ClientException("Could not forward the request. Headers had already been sent.");

        if( $avoidMultiRedirects )
            $uri = $this->generateSignInURI($data);
        else
            $uri = $this->uri('/signin');

        return header('Location: ' . $uri, TRUE, 307);

    }

    /**
     * Query the API and return response data
     * @param $resource
     * @param string $using
     * @param array $with
     *
     * @return object
     * @throws ClientException
     */
    public function fetch($resource, $using=self::GET, $with=[]){

        $response = $this->asObj($resource, $using, $with);

        return $response->data;

    }

    /**
     * Sends a GET request to a resource
     * @param $resource
     * @param array $with
     *
     * @return mixed
     */
    public function get($resource, $with=[]){
        return $this->fetch($resource, self::GET, $with);
    }

    /** Sends a POST request to a resource
     * @param $resource
     * @param array $with
     *
     * @return mixed
     */
    public function post($resource, $with=[]){
        return $this->fetch($resource, self::POST, $with);
    }

    /**
     * Sends a PUT request to a resource
     * @param $resource
     * @param array $with
     *
     * @return mixed
     */
    public function put($resource, $with=[]){
        return $this->fetch($resource, self::PUT, $with);
    }

    /**
     * Sends a PATCH request
     * @param $resource
     * @param array $with
     *
     * @return mixed
     */
    public function patch($resource, $with=[]){
        return $this->fetch($resource, self::PATCH, $with);
    }

    /**
     * Sends a DELETE request
     * @param $resource
     *
     * @return mixed
     */
    public function delete($resource){
        return $this->fetch($resource, self::DELETE);
    }

}