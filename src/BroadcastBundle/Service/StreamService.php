<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 9:49 AM
 */

namespace BroadcastBundle\Service;

use CoreBundle\Entity\Event;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\Stream;

class StreamService
{
    /**
     * @var StreamService
     */
    private $streamService;
    /**
     * @var IcecastService
     */
    private $icecastService;

    /**
     * StreamService constructor.
     * @param StreamService $streamService
     */
    public function __construct(StreamService $streamService,IcecastService $icecastService)
    {
        $this->streamService = $streamService;
        $this->icecastService = $icecastService;
    }

    /**
     * @param Show $show
     * @param Event $event
     */
    public function createStream($show,$event = null)
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