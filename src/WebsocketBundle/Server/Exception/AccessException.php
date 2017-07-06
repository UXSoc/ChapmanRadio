<?php

namespace WebsocketBundle\Server\Exception;

use Throwable;
use WebsocketBundle\Server\Packets\Packet;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/4/17
 * Time: 11:30 PM
 */
class AccessException extends SocketException
{
    public function __construct($message = "", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}