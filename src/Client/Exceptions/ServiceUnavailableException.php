<?php
/**
 * StudentConnect API Client - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client\Exceptions;


class ServiceUnavailableException extends HttpException {

    protected $status = 503;

}