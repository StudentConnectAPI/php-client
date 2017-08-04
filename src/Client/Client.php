<?php
/**
 * StudentConnect API Client - Client Class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.5
 */

namespace StudentConnect\API\Client;

use Monolog\Logger;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Client as HTTPClient;
use StudentConnect\API\Client\Auth\HMAC\Headers;
use \StudentConnect\API\Client\Auth\HMAC\Settings;
use StudentConnect\API\Client\Auth\HMAC\Middleware;
use StudentConnect\API\Client\Auth\HMAC\Request\Signer;
use StudentConnect\API\Client\Exceptions\TokenException;
use StudentConnect\API\Client\Exceptions\ServerException;
use \StudentConnect\API\Client\Exceptions\ClientException;
use StudentConnect\API\Client\Exceptions\AccessDeniedException;
use StudentConnect\API\Client\Exceptions\ResourceNotFoundException;
use StudentConnect\API\Client\Exceptions\ServiceUnavailableException;

class Client{

    const VERSION = '1.0';

    const GET     = 'GET';
    const POST    = 'POST';
    const PATCH   = 'PATCH';
    const DELETE  = 'DELETE';

    const SUCCESS = 'success';
    const ERROR   = 'error';

    const TOKEN_FIELD = '__tkn';

    /**
     * default client headers
     * @var array
     */
    protected $headers = [
        'User-Agent'        => ( 'StudentConnect API Client v' . self::VERSION ),
        'Accept'            => 'application/json; charset=UTF-8',
        'X-Client-Version'  => self::VERSION,
        'X-Client-Platform' => ( PHP_OS . '/' . PHP_SAPI . ' PHP ' . PHP_VERSION )
    ];

    /**
     * @var Configuration|null
     */
    protected $cfg  = NULL;

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
     * Creates new Client instance.
     *
     * @param $endpoint
     * @param $key
     * @param $secret
     */
    public function __construct($endpoint='http://localhost', $key=NULL, $secret=NULL) {

        $this->configure( new Configuration($endpoint, $key, $secret) );

    }

    /**
     * Configures client
     * @param Configuration $configuration
     */
    public function configure( Configuration $configuration){
        $this->cfg = $configuration;
    }

    /**
     * Push request logger down the handler stack
     * @param Logger $logger
     * @param HandlerStack $stack
     * @param MessageFormatter|NULL $formatter
     */
    protected function pushRequestLogger(Logger $logger,  HandlerStack $stack, MessageFormatter $formatter=NULL){

        if( $logger ){

            $formatter = empty($formatter) ? new MessageFormatter(MessageFormatter::DEBUG) : $formatter;
            $tap       = \GuzzleHttp\Middleware::log($logger, $formatter);

            $stack->push( $tap );

        }
        else
            throw new \InvalidArgumentException("No logger specified... .");

    }

    /**
     * Generates full URI for an api resource
     * @param $resource
     * @param array $params
     *
     * @return string
     */
    protected function uri($resource, $params=[]){

        $uri    = trim( trim($this->cfg->getEndpoint(), '/') . '/' . ltrim($resource, '/'), '/');
        $query  = '';

        if( $params and is_array($params) and count($params) )
            $query = ( '?' . http_build_query($params) );

        return ( $uri . $query );

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

            //init stack
            $stack = HandlerStack::create();

            if ( $this->token ){

                //setup to send the token with each request

                $this->addHeader(Headers::AUTHORIZATION, ( "TOKEN " . $this->token->getValue() ) );

            }
            else{

                //setup client to send signed requests

                $signer     = new Signer(Settings::PROVIDER);
                $middleware = new Middleware($signer, $this->cfg->getKey(), $this->cfg->getSecret());

                $stack->push($middleware);

            }

            //enable debug
            if( $logger = $this->cfg->getLogger() )
                $this->pushRequestLogger($logger, $stack);

            //create the client
            $this->HTTPClient = new HTTPClient([
                'headers' => &$this->headers,
                'timeout' => $this->cfg->getRequestTimeout(),
                'handler' => $stack
            ]);

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

        $url = $this->uri($resource, ( self::GET == $method ? $data : NULL ) );

        //save last request data
        $this->lastRequest = [
            'verb'     => $method,
            'resource' => $resource,
            'data'     => count($data) ? $data : []
        ];

        try{

            switch ( $method ){

                case self::GET:
                    return $this->http()->get($url);

                case self::POST:
                    return $this->http()->post($url, [ 'json' => $data ]);

                case self::PATCH:
                    return $this->http()->patch($url, [ 'json' => $data ]);

                case self::DELETE:
                    return $this->http()->delete($url);

                default:
                    throw new ClientException("Method not supported: $method.");

            }

        }
        catch (\GuzzleHttp\Exception\ClientException $e){

            //the error is on the client side

            $this->rawResponse = $e->getResponse()->getBody()->__toString();

            switch ( $e->getCode() ){

                case 400:
                    throw new ClientException( $e->getMessage(), $e );

                case 403:
                    throw new AccessDeniedException($e->getMessage(), $e);

                case 404:
                    throw new ResourceNotFoundException($e->getMessage(), $e);

            }

            throw new ClientException( $e->getMessage(), $e );

        }
        catch (\GuzzleHttp\Exception\ServerException $e){

            //the error is on the API side

            $this->rawResponse = $e->getResponse()->getBody()->__toString();

            throw new ServerException( $e->getMessage(), $e );

        }

    }

    /**
     * Get response as object
     * @param $resource
     * @param string $method
     * @param array $data
     *
     * @return mixed
     * @throws ClientException
     * @throws ServiceUnavailableException
     */
    protected function asObj($resource, $method=self::GET, $data=[]){

        $json = $this->asRaw($resource, $method, $data);

        if( empty($json) )
            throw new ClientException("Empty response from the API for resource {$resource}.");

        $obj = @json_decode( $json );

        if( $json and $obj and isset($obj->status) ){

            if( self::SUCCESS == $obj->status ){

                //successful request

                if( ! isset($obj->data) )
                    $obj->data = NULL;

                if( ! isset($obj->meta) )
                    $obj->meta = NULL;

                $this->data = isset($obj->data) ? $obj->data : new \stdClass();
                $this->meta = isset($obj->meta) ? $obj->meta : new \stdClass();

                return $obj;

            }
            else
                throw new ClientException("{$obj->code} Error: {$obj->message}");
        }

        throw new ServiceUnavailableException("Invalid response from the API for resource {$resource}.");

    }

    /**
     * Get raw response
     * @param $resource
     * @param string $method
     * @param array $data
     *
     * @return string
     */
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

    /**
     * Returns details of the last request
     * @return array
     */
    public function getLastRequestDetails(){
        return $this->lastRequest;
    }

    /**
     * Retrieve current token
     * @return null|Token
     */
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

        //drop the http client
        $this->HTTPClient = NULL;

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
        $token = $this->retrieveToken(); $this->setToken($token);
    }

    /**
     * Retrieves latest response's data field
     * @param null $key
     * @param null $default
     *
     * @return null|\stdClass
     */
    public function getResponseData($key=NULL, $default=NULL){
        return $key ? ( isset($this->data->$key) ? $this->data->$key : $default ) : $this->data;
    }

    /**
     * Retrieves latest response's meta field
     * @param $key
     * @param null $default
     *
     * @return null|\stdClass
     */
    public function getResponseMeta($key, $default=NULL){
        return $key ? ( isset($this->meta->$key) ? $this->meta->$key : $default ) : $this->meta;
    }

    /**
     * Retrieves current profile associated with the token
     * @return object|null
     * @throws ClientException
     */
    public function getCurrentProfile(){

        try{

            if( $this->hasToken() ){

                $account = $this->asObj( '/profile' );

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
     * Generates the sign in URI
     * @param bool $forward
     * @param bool $with_token
     *
     * @return null|string
     * @throws ClientException
     */
    public function generateSignInURI($forward=FALSE, $with_token=FALSE){

        $signIn = $this->asObj('/signin', self::POST, [
            'forward'   => intval($forward),
            'with_token'=> $with_token
        ]);

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

        if( ! $this->token )
            throw new ClientException("Authorization token is missing. You need a valid token in order to forward sign in requests.");

        $params = [];

        if( ! isset($_SERVER['REQUEST_METHOD']) )
            $_SERVER['REQUEST_METHOD'] = self::GET;

        if( ( $_SERVER['REQUEST_METHOD'] == self::POST ) )

            if( isset($_POST[self::TOKEN_FIELD]) ); else{
                //we don't have the token in the request
                $params[self::TOKEN_FIELD] = $this->token->getValue();

            }

        if( $avoidMultiRedirects )
            $uri = $this->generateSignInURI($data, FALSE, count($params));
        else
            $uri = $this->uri('/signin', $params);

        //redirect with 307 status
        return header('Location: ' . $uri, TRUE, 307);

    }

    /**
     * Generates a token form field
     * @param bool $echo
     * @param string $name [optional] the token field name
     *
     * @return string
     * @throws ClientException
     */
    public function tokenField($echo=TRUE, $name=self::TOKEN_FIELD){

        if( ! $this->token )
            throw new ClientException("Token missing. Make sure you authorize before requesting a token field.");

        //construct html
        $field = '<input type="hidden" name="{name}" id="{name}" value="{token}"/>';
        $field = str_ireplace(
            ['{name}', '{token}'],
            [$name, $this->token->getValue()],
            $field
        );

        if( $echo )
            echo $field;

        return $field;

    }

    /**
     * Echoes the token form field
     * @param string $fieldName
     *
     * @throws ClientException
     */
    public function tokenizeForm($fieldName=self::TOKEN_FIELD){
        $this->tokenField(TRUE, $fieldName);
    }

    /**
     * Adds token query to an url
     * @param $uri
     * @param string $param
     *
     * @return string
     */
    public function tokenizeURI($uri, $param=self::TOKEN_FIELD){
        $uri = new Uri($uri); return $uri->withQuery( http_build_query( [ $param => $this->token->getValue() ] ) )->__toString();
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

        return isset($response->data) ? $response->data : NULL;

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