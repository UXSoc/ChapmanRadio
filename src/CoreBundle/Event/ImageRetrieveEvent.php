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
    private $path;

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

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

}