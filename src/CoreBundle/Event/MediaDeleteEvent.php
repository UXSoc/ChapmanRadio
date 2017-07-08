<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 9:45 PM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class MediaDeleteEvent extends Event
{
    const NAME = "media.delete";

    private $media;

    /**
     * ImageEvent constructor.
     * @param Media $media
     *
     */
    function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function getMedia()
    {
        return $this->media;
    }
}