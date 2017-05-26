<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/24/17
 * Time: 11:21 PM
 */

namespace CoreBundle\Helper;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class RestfulJsonResponse extends JsonResponse
{
    /** @var  string */
    private $message;

    private $payload;

    /** @var array  */
    private $errors = [];



    public function __construct($status = 200, array $headers = array(), $json = false)
    {
        parent::__construct(null, $status, $headers, $json);
    }

    public function setMessage($message)
    {
        $this->message = $message;
        $this->setData($this->payload);
    }

    public  function setData($payload = array())
    {
        $this->payload = $payload;

        if(count($this->errors) == 0 && $this->statusCode < 400) {
            return parent::setData([
                'success' => true,
                'message' =>  $this->message,
                "result" => $this->payload]);
        }
        return parent::setData([
            "success" => false,
            "message" => $this->message,
            "errors" => $this->errors
        ]);

    }


    /**
     * @param ConstraintViolationInterface  $error
     */
    public function addError($error)
    {
        $this->addKeyError($error->getPropertyPath(), $error->getMessage());
    }

    public function addKeyError($key,$error)
    {
        $this->errors[] = ["field" => $key, "message" => $error];
        $this->setData($this->payload);
    }

    /**
     * @param array | ConstraintViolationList $errors
     */
    public  function addErrors($errors)
    {
        if($errors instanceof ConstraintViolationList) {
            foreach ($errors as $error) {
                $this->addKeyError($error->getPropertyPath(), $error->getMessage());
            }
            return;
        }
        foreach ($errors as $key => $value ) {
            $this->addKeyError($key,$value);
        }
    }

    public function hasErrors()
    {
        return count($this->errors) != 0;
    }

}