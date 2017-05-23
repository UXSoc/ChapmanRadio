<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/22/17
 * Time: 11:41 PM
 */

namespace CoreBundle\Helper;


class RestfulError
{
    private  $field;
    private $message;
    function __construct($field,$message)
    {
        $this->field = $field;
        $this->message = $message;
    }

    public  function  getField()
    {
        return $this->field;
    }

    public  function  getMessage()
    {
        return $this->message;
    }

}