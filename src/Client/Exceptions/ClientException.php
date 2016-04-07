<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client\Exceptions;

use GuzzleHttp\Exception\BadResponseException;

class ClientException extends \Exception{

    protected $responseRaw;
    protected $responseObj;

    protected $status = 400;

    protected $message;

    protected $request = '';

    public function __construct( $message, BadResponseException $e = NULL ) {

        $this->status      = $e ? $e->getCode() : 400;
        $this->responseRaw = $e ? $e->getResponse()->getBody()->__toString() : '';
        $this->responseObj = $e ? @json_decode( $this->responseRaw ) : new \stdClass();

        if( $this->responseObj and isset($this->responseObj->message) )
            $message = strval( $this->responseObj->message );

        if( $this->responseObj and isset($this->responseObj->request) )
            $this->request = $this->responseObj->request;

        parent::__construct( $message, $this->status );

    }

    public function getRequest(){
        return $this->request;
    }

}