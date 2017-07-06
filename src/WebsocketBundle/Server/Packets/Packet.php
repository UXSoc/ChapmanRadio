<?php
namespace WebsocketBundle\Server\Packets;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/5/17
 * Time: 6:08 PM
 */
interface Packet
{
    public function groups();

    public function getType();

    public function getPayload();

    const MESSAGE = 'MESSAGE';
    const USERNOTICE = 'USERNOTICE';
    const EXCEPTION = 'EXCEPTION';
    const AUTH = 'AUTH';
}