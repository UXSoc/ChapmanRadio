<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 9:49 AM
 */

namespace CoreBundle\Service;

use BroadcastBundle\Entity\Stream;
use BroadcastBundle\Service\IcecastService;
use CoreBundle\Entity\Event;
use CoreBundle\Entity\Show;

class StreamService
{
    /**
     * @var IcecastService
     */
    private $icecastService;

    /**
     * StreamService constructor.
     * @param StreamService $streamService
     */
    public function __construct(IcecastService $icecastService)
    {
        $this->icecastService = $icecastService;
    }

    /**
     * @param Show $show
     * @param Event $event
     */
    public function createStream($show,Event $event = null)
    {
        $stream = new Stream();
        $stream->setUsername($show->getSlug());
        $stream->setMount(substr(bin2hex(random_bytes(12)),5));
        $stream->setPassword(substr(bin2hex(random_bytes(12)),10));
        $stream->setRecording(substr(bin2hex(random_bytes(12)),5));
        if($event != null)
            $event->setStream($stream);
        return $stream;

    }




}