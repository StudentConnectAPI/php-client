<?php
/**
 * StudentConnect - API Auth HMAC Headers definitions
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.1
 */

namespace StudentConnect\API\Client\Auth\HMAC;

class Headers{

    const AUTHORIZATION = 'Authorization';
    const CONTENT_TYPE  = 'Content-Type';

    const NONCE         = 'X-Nonce';
    const TOKEN         = 'X-Token';
    const TIMESTAMP     = 'X-Timestamp';

}