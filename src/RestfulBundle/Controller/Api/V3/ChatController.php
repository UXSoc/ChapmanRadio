<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/4/17
 * Time: 2:39 PM
 */

namespace RestfulBundle\Controller\Api\V3;


use Firebase\JWT\JWT;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * @Route("/api/v3/")
 */
class ChatController extends FOSRestController
{

    /**
     * @Rest\Get("chat",
     *     options = { "expose" = true },
     *     name="get_chat_token")
     */
    public function getChatAction(Request $request)
    {
        $token = JWT::encode([
            'iss' => $request->getUri(),
            'sub' => 'chat',
            'iat' => (int)(new \DateTime('now'))->format('U'),
            "nbf" => (int)(new \DateTime('now'))->format('U')
        ],$this->container->getParameter('env(SYMFONY_SECRET)'));

        $this->view(['token' => $token)]);

    }

}