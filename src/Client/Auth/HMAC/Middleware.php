<?php
/**
 * StudentConnect - API HMAC Auth Middleware
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.1
 */

namespace StudentConnect\API\Client\Auth\HMAC;

use Psr\Http\Message\RequestInterface;
use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\Request\Guzzle as RequestWrapper;

class Middleware extends HmacAuthMiddleware{

    /**
     * Signs HMAC requests
     * @param RequestInterface $request
     *
     * @return mixed
     */
    public function signRequest(RequestInterface $request) {

        //add Date header
        if ( ! $request->hasHeader(Headers::TIMESTAMP) ) {
            $time = new \DateTime();
            $time->setTimezone(new \DateTimeZone( Settings::TIMEZONE ));
            $request = $request->withHeader(Headers::TIMESTAMP, $time->format( Settings::TIMESTAMP_FORMAT ));
        }

        //add Content-Type header
        if ( ! $request->hasHeader(Headers::CONTENT_TYPE) ) {
            $request = $request->withHeader(Headers::CONTENT_TYPE, $this->defaultContentType);
        }

        //add Nonce header
        if ( ! $request->hasHeader(Headers::NONCE) ) {
            $request = $request->withHeader(Headers::NONCE, $this->generateNonce());
        }

        //generate Authorization header
        $authorization = $this->requestSigner->getAuthorization(new RequestWrapper($request), $this->id, $this->secretKey);

        return $request->withHeader('Authorization', $authorization);

    }


    /**
     * Generates random string
     * @param int $length
     * @param string $keyspace
     *
     * @return string
     */
    protected function generateNonce($length=16, $keyspace='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ|[]{}()*&^%$#@!+-_±'){

        $str = '';

        $max = ( mb_strlen($keyspace, '8bit') - 1 );

        for ($i = 0; $i < $length; ++$i) {
            $int  = function_exists('random_int') ? random_int(0, $max) : rand(0, $max);
            $str .= $keyspace[$int];
        }

        return $str;

    }

}