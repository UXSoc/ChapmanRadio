<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Controller;


use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\WrapperNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Config\Tests\Util\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $content = $this->get('request_stack')->getCurrentRequest()->getContent();
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
     * @param SuccessWrapper|ErrorWrapper $wrapper
     * @param $data
     * @param null $format
     * @param array $context
     * @return JsonResponse
     */
    public function restful($normalizers,$data,$status = 200, $format = null, array $context = array())
    {
        $normalizer =  new Serializer($normalizers);
        return new JsonResponse($normalizer->normalize($data,$format,$context),$status);
    }

    /**
     * @param $message
     * @param int $status
     * @return JsonResponse
     */
    public function messageError($message,$status = 400)
    {
        return $this->restful([new WrapperNormalizer()],new ErrorWrapper($message),$status);
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