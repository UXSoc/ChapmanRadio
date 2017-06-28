<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/27/17
 * Time: 7:39 AM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Image;
use Imagine\Gd\Imagine;
use Symfony\Component\EventDispatcher\Event;

class ImageEvent extends Event
{
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