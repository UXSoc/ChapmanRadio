<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Helper;


use Symfony\Component\HttpFoundation\JsonResponse;

class RestfulHelper
{
    /**
     * @param $code
     * @param $message
     * @param $errors
     * @param RestfulError $additionalErrors
     */
    static function error($code,$message,$errors,$additionalErrors = [])
    {

        $errorCollection = array();
        foreach($errors as $error)
        {
            $errorCollection[] = ["field" => $error->getPropertyPath(), "message" => $error->getMessage()];
        }
        foreach ($additionalErrors as $error) {
            $errorCollection[] = ["field" => $error->getField(), "message" => $error->getMessage()];
        }
        return new JsonResponse([
            "success" => false,
            "message" => $message,
            "errors" => $errorCollection
        ],$code);
    }

    static function success($message,$payload = [])
    {
        return new JsonResponse([
            'success' => true,
            'message' => $message,
            "result" => $payload
        ]);

    }

}