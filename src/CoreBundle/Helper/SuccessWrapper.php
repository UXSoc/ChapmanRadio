<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/27/17
 * Time: 6:48 PM
 */

namespace CoreBundle\Helper;


use Symfony\Component\Serializer\Serializer;

class SuccessWrapper
{
    private $message;
    private $payload;

    /**
     * SuccessWrapper constructor.
     * @param mixed $payload
     * @param string $message
     */
    function __construct($payload = null, $message = null)
    {
        $this->payload = $payload;
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

    /**
     * @param [] $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

    }

    public function getPayload()
    {
        return $this->payload;
    }

}