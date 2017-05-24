<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
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
        return new ParameterBag($this->getJsonPayloadAsMapping());
    }

    public function getJsonPayloadAsMapping(){
        $result =  json_decode($this->getJsonPayload(),true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new BadRequestHttpException("Content is not valid json");
        }
        return $result;
    }

    public function getJsonPayload()
    {
        $content = $this->container->get('request_stack')->getCurrentRequest()->getContent();
        if(empty($content))
        {
            throw new BadRequestHttpException("Content is empty");
        }
        return $content;
    }

    public  function denromalizeMapping($mapping,$class){
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        return $serializer->denormalize($mapping,$class);
    }


    /**
     * @param $entity
     * @return ConstraintViolationList
     */
    public function validateEntity($entity)
    {
        return $this->get('validator')->validate($entity);
    }



}