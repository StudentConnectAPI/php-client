<?php
/**
 * StudentConnect - API HMAC Auth Digest Version2
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.1
 */

namespace StudentConnect\API\Client\Auth\HMAC\Digest;

use Acquia\Hmac\Digest\Version1;
use Acquia\Hmac\RequestSignerInterface;
use Acquia\Hmac\Request\RequestInterface;
use StudentConnect\API\Client\Auth\HMAC\Headers;

class Version2 extends Version1{

    /*
     * HMAC Algorithm to use
     */
    const ALGORITHM = 'sha256';

    public function __construct( $algorithm = self::ALGORITHM ) {

        if( empty($algorithm) )
            throw new \Exception("Algorithm needs to be one of the values returned by hash_algos()!");

        parent::__construct( $algorithm );

    }

    /**
     * Makes a digest message
     * @param RequestSignerInterface $requestSigner
     * @param RequestInterface $request
     *
     * @return string
     */
    protected function getMessage(RequestSignerInterface $requestSigner, RequestInterface $request) {

        $parts = [
            $this->getMethod($request),
            $this->getHash($request),
            $this->getContentType($requestSigner, $request),
            $this->getTimestamp($requestSigner, $request),
            $this->getCustomHeaders($requestSigner, $request),
            $this->getResource($request)
        ];

        //TODO add debug to file ...
        $data = join("\n", $parts);
        file_put_contents('/var/www/debug.txt', $data, FILE_APPEND);

        return join("\n", $parts);

    }

    /**
     * Generates a hash based the request body and nonce header
     * @param RequestInterface $request
     *
     * @return string
     */
    protected function getHash(RequestInterface $request) {
        return md5( $request->getBody() . $request->getHeader(Headers::NONCE) );
    }

}