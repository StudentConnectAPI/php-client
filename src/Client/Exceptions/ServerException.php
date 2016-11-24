<?php
/**
 * StudentConnect API Client - Server Exception class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace StudentConnect\API\Client\Exceptions;

class ServerException extends HttpException {

    protected $status = 500;

}