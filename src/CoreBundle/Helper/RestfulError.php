<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved

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