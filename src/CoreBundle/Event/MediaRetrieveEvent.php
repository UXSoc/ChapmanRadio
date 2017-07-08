<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 9:51 PM
 */

namespace CoreBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class MediaRetrieveEvent extends Event
{
    const NAME = "media.retrieve";
}