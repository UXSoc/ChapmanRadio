<?php

namespace WebsocketBundle\Server\Exception;

use Throwable;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/4/17
 * Time: 11:30 PM
 */
class AccessException extends \RuntimeException
{
    public function __construct($message = "", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}