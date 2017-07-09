<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 7:57 PM
 */

namespace CoreBundle\Helper;


class MediaFilterBuilder
{
    private $operations = [];
    function __construct()
    {
    }

    public function crop($x,$y,$width,$height)
    {
        $this->operations[] = [
            'type' => 'crop',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        ];
    }

    public function getResult()
    {
        return [
            'operations' => $this->operations
        ];
    }
}