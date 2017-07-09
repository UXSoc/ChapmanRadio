<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 9:51 PM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class MediaRetrieveEvent extends Event
{
    const NAME = "media.retrieve";


    private $media;
    private $path;

    /**
     * ImageEvent constructor.
     * @param Media $image
     * @param callable $callback
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

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }
}