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
    private $orginal;

    /**
     * ImageEvent constructor.
     * @param Media $image
     * @param callable $callback
     *
     */
    function __construct(Media $media,$orignal = false)
    {
        $this->media = $media;
        $this->orginal = $orignal;
    }

    public function getOrignal()
    {
        return $this->orginal;
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