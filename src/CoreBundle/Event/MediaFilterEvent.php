<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 9:27 PM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class MediaFilterEvent extends Event
{
    const NAME = "media.filter";

    private $media;
    private $filter;
    private $isHidden;
    private $duplicate;

    /**
     * ImageEvent constructor.
     * @param Media $media
     * @param callable $callback
     *
     */
    function __construct(Media $media, $filter, $isHidden)
    {
        $this->media = $media;
        $this->filter = $filter;
        $this->isHidden = $isHidden;

    }

    public function getDuplicate()
    {
        return $this->duplicate;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getIsHidden()
    {
        return $this->isHidden;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function setMedia($media)
    {
        $this->media = $media;
    }
}