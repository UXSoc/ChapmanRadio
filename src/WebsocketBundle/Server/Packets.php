<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/5/17
 * Time: 12:09 PM
 */

namespace WebsocketBundle\Server;


class Packets
{
    //------------------------------------------
    const NOTICE = "NOTICE";

    //------------------------------------------
    const USER_NOTICE = "USERNOTICE";
    const VERIFIED = "VERIFIED";
    const FAILED_VERIFIED = "FAILED_VERIFIED";
    const ACCESS_EXCEPTION = "ACCESS_EXCEPTION";

    //------------------------------------------
    const MSG = "MSG";
    const MSG_PRIVATE = "PRIVATE";
    const MSG_PUBLIC = "PUBLIC";

}