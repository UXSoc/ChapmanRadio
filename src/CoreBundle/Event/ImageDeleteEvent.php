<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 8:26 PM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Image;
use Symfony\Component\EventDispatcher\Event;

class ImageDeleteEvent extends Event
{
    const NAME = "image.delete";

    private $image;

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
}