<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/27/17
 * Time: 11:42 AM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Image;
use Symfony\Component\EventDispatcher\Event;

class SaveImageEvent extends ImageEvent
{
    private $image;
    private $path;
    private $options;
    private $callback;

    /**
     * ImageEvent constructor.
     * @param Image $image
     * @param callable $callback
     *
     * function(ImageInterface $image)
     * {
     * }
     */
    function __construct(Image $image, $options = array(), $callback = null)
    {
        $this->callback = $callback;
        $this->options = $options;
        parent::__construct($image);
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getOptions()
    {
        return $this->options;
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