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
use Symfony\Component\Validator\Validation;

class BaseController extends Controller
{
    public function getJsonPayload(){
        $content = $this->container->get('request_stack')->getCurrentRequest()->getContent();
        if(empty($content))
        {
            throw new BadRequestHttpException("Content is empty");
        }
        $result =  json_decode($content,true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new BadRequestHttpException("Content is not valid json");
        }
        return new ParameterBag($result);
    }
}