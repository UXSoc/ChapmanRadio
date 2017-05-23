<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/20/17
 * Time: 10:03 PM
 */

namespace CoreBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Tests\Util\Validator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends Controller
{
    public function getJsonPayloadAsParameterBag()
    {
        return new ParameterBag($this->getJsonPayload());
    }

    public function getJsonPayloadAsMapping(){
        $result =  json_decode($this->getJsonPayload(),true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new BadRequestHttpException("Content is not valid json");
        }
        return $result;
    }

    public  function getJsonPayload()
    {
        $content = $this->container->get('request_stack')->getCurrentRequest()->getContent();
        if(empty($content))
        {
            throw new BadRequestHttpException("Content is empty");
        }
        return $content;
    }

    public  function JsonDeserializer($json,$class){
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $mapping = $serializer->decode($json);
        return $serializer->denormalize($mapping,$class);
    }

    /**
     * @param $entity
     * @return ConstraintViolationListInterface
     */
    public function validateEntity($entity)
    {
        $validate = $this->get('validator');
        return $validate->validate($entity);
    }

    public function getErrors($entity)
    {
        $errors = $this->validateEntity($entity);
        $result = array();
        foreach($errors as $error)
        {
            $result[$error->getPropertyPath()] = $error->getMessage();
        }
        return $result;
    }


}