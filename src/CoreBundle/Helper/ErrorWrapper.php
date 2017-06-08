<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/27/17
 * Time: 6:47 PM.
 */

namespace CoreBundle\Helper;

class ErrorWrapper
{
    private $errors = [];
    private $message;

    public function __construct($message = null)
    {
        $this->message = $message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function addError($key, $error)
    {
        $this->errors[] = ['field' => $key, 'message' => $error];
    }
}
