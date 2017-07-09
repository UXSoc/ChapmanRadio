<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 8:44 PM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class MediaSaveEvent extends Event
{
    const NAME = "media.save";

    private $media;
    private $options;

    /**
     * ImageEvent constructor.
     * @param Media $media
     * @param callable $callback
     *
     */
    function __construct(Media $media,$options = [])
    {
        $this->media = $media;
        $this->options = $options;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function getOptions(){
        return $this->options;
    }

}