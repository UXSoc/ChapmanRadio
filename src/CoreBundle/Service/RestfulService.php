<?php
namespace CoreBundle\Service;


use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\WrapperNormalizer;
use PHPUnit\Framework\Error\Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestfulService
{
    /** @var ValidatorInterface Validation  */
    private $validate;

    /**
     * RestfulService constructor.
     * @param ValidatorInterface $validate
     */
    function __construct($validate)
    {
        $this->validate = $validate;
    }

    /**
     * @param SuccessWrapper|ErrorWrapper $wrapper
     * @param $data
     * @param null $format
     * @param array $context
     * @return JsonResponse
     */
    public function response($normalizers, $data, $status = 200, $format = null, array $context = array())
    {
        $normalizer = new Serializer($normalizers);
        return new JsonResponse($normalizer->normalize($data, $format, $context), $status);
    }

    /**
     * @param $message
     * @param int $status
     * @return JsonResponse
     */
    public function errorResponse($message, $status = 400)
    {
        return $this->response([new WrapperNormalizer()], new ErrorWrapper($message), $status);
    }

    /**
     * @param $normalizers
     * @param $message
     * @param $payload
     * @return JsonResponse
     */
    public function successResponse($normalizers,$payload,$message)
    {
        return $this->response(array_merge([new WrapperNormalizer()],$normalizers),new SuccessWrapper($payload,$message));
    }

    public function errorResponseValidate($payload,$message,$status = 400)
    {
        $errorResponse = new ErrorWrapper($message);
        $errorResponse->addErrors($this->validate($payload));
        if($errorResponse->hasErrors() === false)
            return false;
        return $this->response([new WrapperNormalizer()],$errorResponse,$status);

    }
    /**
     * @param $entity
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validate($object)
    {
        return $this->validate->validate($object);
    }

}
