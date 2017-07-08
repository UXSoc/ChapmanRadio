<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 8:25 PM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Image;
use Symfony\Component\EventDispatcher\Event;

class ImageRetrieveEvent extends Event
{
    const NAME = "image.retrieve";

    private $image;
    private $fullPath;
    private $path;
    private $uriPath;

    /**
     * ImageEvent constructor.
     * @param Image $image
     * @param callable $callback
     *
     */
    function __construct(Image $image)
    {
        $this->image = $image;
    }


    public function getImage()
    {
        return $this->image;
    }

    public function setFullPath($path)
    {
        $this->fullPath = $path;
    }

    public function getFullPath()
    {
        return $this->fullPath;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getUriPath()
    {
        return $this->uriPath;
    }

    public function setUriPath($path)
    {
        $this->uriPath = $path;
    }
}