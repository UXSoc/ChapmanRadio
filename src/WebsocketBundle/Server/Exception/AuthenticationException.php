<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/5/17
 * Time: 6:18 PM
 */

namespace WebsocketBundle\Server\Exception;


use Throwable;

class AuthenticationException extends SocketException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}