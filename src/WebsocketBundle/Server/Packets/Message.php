<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/5/17
 * Time: 6:32 PM
 */

namespace WebsocketBundle\Server\Packets;


use Ratchet\ConnectionInterface;

class Message implements Packet
{

    private  $message;
    private  $from;
    private  $to;
    private  $timeStamp;

    function __construct($message,ConnectionInterface $from,ConnectionInterface $to, $timeStamp = 'now')
    {
        $this->message = $message;
        $this->from = $from;
        $this->to = $to;
        $this->timeStamp = new \DateTime($timeStamp );
    }

    public function groups()
    {
        return ['list'];
    }

    public function getType()
    {
        return Packet::MESSAGE;
    }

    public function getPayload()
    {
        $result = [
            'message' => $this->message,
            'timestamp' => $this->timeStamp
        ];
        if(isset($this->to->user))
        {
            $result['user'] = $this->to->user;
        }
        return $result;
    }
}