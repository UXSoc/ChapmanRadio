<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/5/17
 * Time: 6:16 PM
 */

namespace WebsocketBundle\Server\Exception;


use WebsocketBundle\Server\Packets\Packet;

class SocketException extends \RuntimeException implements Packet
{

    public function getType()
    {
        return Packet::EXCEPTION;
    }

    public function getPayload()
    {
        return [
            "exception" => get_class($this),
            'message' => $this->message
        ];
    }

    public function groups()
    {
       return null;
    }
}