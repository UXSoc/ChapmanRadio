<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/4/17
 * Time: 10:00 PM
 */

namespace CoreBundle\Helper;


use CoreBundle\Normalizer\WrapperNormalizer;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RestfulEnvelope
{
    private $errorWrapper;
    private $normalizers;
    private  $message;
    private  $payload;
    private  $status;

    function __construct()
    {
        $this->normalizers = [];
        $this->errorWrapper = new ErrorWrapper();
        $this->status = 200;
        $this->message = null;
    }

    public static function restfulBuilder()
    {
        return new RestfulEnvelope();
    }

    public static function successResponseTemplate($message = null, $payload = null, $normalizers = [])
    {
        return RestfulEnvelope::restfulBuilder()->setMessage($message)->setStatus(200)->setNormalizers($normalizers)->setPayload($payload);
    }

    public static function errorResponseTemplate($message = null, $errors = [])
    {
        return RestfulEnvelope::restfulBuilder()->setMessage($message)->setStatus(400)->addErrors($errors);
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setNormalizers($normalizers)
    {
        $this->normalizers = $normalizers;
        return $this;
    }

    public function addNormalizer($normalizer)
    {
        array_merge($this->normalizers,$normalizer);
        return $this;
    }

    public function setPayload($payload)
    {
         $this->payload = $payload;
         return $this;
    }

    /**
     * @param array | ConstraintViolationListInterface $errors
     * @return $this
     */
    public function addErrors($errors)
    {
        if($errors instanceof ConstraintViolationList) {
            foreach ($errors as $error) {
                $this->errorWrapper->addError($error->getPropertyPath(), $error->getMessage());
            }
        }
        else
        {
            foreach ($errors as $key => $value) {
                $this->errorWrapper->addError($key, $value);
            }
        }
        return $this;
    }

    /**
     * @param Form $form
     * @return $this
     */
    public function addFormErrors($form){
       $errors = $form->getErrors(true);

       /** @var FormError $error */
        foreach ($errors as $error)
       {
           if($error->getCause() == null)
           {
               $this->setMessage($error->getMessage());
               break;
           }
           else
           {
               /** @var ConstraintViolation $cause */
               $cause = $error->getCause();
               $this->addErrors([$cause->getPropertyPath() => $error->getMessage()]);
           }
       }
       return $this;
    }

    public function processError($children)
    {
        foreach ($children as $child)
        {

        }

    }

    public function setMessage($message)
    {
        $this->message= $message;
        return $this;
    }

    public function response($format = [],$context = [])
    {
        if($this->status >= 400)
        {
            $this->normalizers[] = new WrapperNormalizer();

            $normalizer = new Serializer($this->normalizers);
            $this->errorWrapper->setMessage($this->message);
            return new JsonResponse($normalizer->normalize($this->errorWrapper, $format, $context), $this->status);
        }
        else
        {
            $this->normalizers[] = new WrapperNormalizer();

            $normalizer = new Serializer( $this->normalizers);
            return new JsonResponse($normalizer->normalize(new SuccessWrapper($this->payload,$this->message), $format, $context), $this->status);

        }
    }


}