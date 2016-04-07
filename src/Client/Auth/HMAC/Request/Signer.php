<?php
/**
 * StudentConnect - HMAC RequestSigner
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client\Auth\HMAC\Request;

use Acquia\Hmac\Signature;
use Acquia\Hmac\Exception;
use Acquia\Hmac\RequestSigner;
use Acquia\Hmac\Digest\DigestInterface;
use Acquia\Hmac\Request\RequestInterface;
use StudentConnect\API\Client\Auth\HMAC\Headers;
use StudentConnect\API\Client\Auth\HMAC\Digest\Version2;
use StudentConnect\API\Client\Auth\HMAC\Exceptions\AuthorizationException;

class Signer extends RequestSigner{

    protected $customHeaders = [
        Headers::NONCE
    ];

    protected $timestampHeaders = [
        Headers::TIMESTAMP
    ];

    /**
     * {@inheritdoc}
     *
     * @param string $provider
     * @param DigestInterface|NULL $digest
     */
    public function __construct($provider='PROVIDER', DigestInterface $digest=NULL) {

        $this->setProvider($provider);

        $digest = $digest ?: new Version2();

        parent::__construct($digest);

    }

    /**
     * {@inheritDoc}
     *
     * @throws AuthorizationException
     */
    public function getSignature(RequestInterface $request) {

        if ( ! $request->hasHeader( Headers::AUTHORIZATION ) ) {

                throw new AuthorizationException("Authorization header required. Requests sent to the API need to be signed or provide an identification token.");
        }

        // Check the provider.
        $header = $request->getHeader( Headers::AUTHORIZATION );
        if ( ( $pos = strpos($header, $this->provider . ' ') ) === false ) {
            throw new AuthorizationException("Invalid provider in authorization header.");
        }

        // Split ID and signature by an unescaped colon.
        $offset         = strlen($this->provider) + 1;
        $credentials    = substr($header, $offset);
        $matches        = preg_split('@\\\\.(*SKIP)(*FAIL)|:@s', $credentials);
        if ( ! isset($matches[1]) ) {
            throw new AuthorizationException('Unable to parse ID and signature from authorization header');
        }

        // Ensure the signature is a base64 encoded string.
        if ( ! preg_match('@^[a-zA-Z0-9+/]+={0,2}$@', $matches[1]) ) {
            throw new AuthorizationException('Invalid signature in authorization header');
        }

        $time       = $this->getTimestamp($request);
        $timestamp  = strtotime($time);
        if ( ! $timestamp ) {
            throw new AuthorizationException("Invalid timestamp. Your authorization request should contain the a timestamp.");
        }

        return new Signature(stripslashes($matches[0]), $matches[1], $timestamp);

    }

}