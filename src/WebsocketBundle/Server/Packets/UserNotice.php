<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/5/17
 * Time: 6:10 PM
 */

namespace WebsocketBundle\Server\Packets;


class UserNotice implements Packet
{
    const VERIFIED = "VERIFIED";

    private  $flag;
    private  $from;

    function __construct($flag,$from)
    {
        $this->flag = $flag;
        $this->from= $from;
    }

    public function getPayload()
    {
        $result = [
            'flag' => $this->flag
        ];

        return $result;
    }

    public function getType()
    {
        return Packet::USERNOTICE;
    }

    public function groups()
    {
        return ['lists'];
    }
}