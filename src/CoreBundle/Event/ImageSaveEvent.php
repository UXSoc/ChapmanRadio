<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 8:23 PM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Image;
use Symfony\Component\EventDispatcher\Event;

class ImageSaveEvent extends Event
{
    const NAME = "image.save";

    private $image;
    private $path;
    private $options;
    private $callback;


    /**
     * ImageEvent constructor.
     * @param Image $image
     * @param callable $callback
     *
     */
    function __construct(Image $image, $options = array(), $callback = null)
    {
        $this->image = $image;
        $this->callback = $callback;
        $this->options = $options;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getOptions()
    {
        return $this->options;
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