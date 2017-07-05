<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/4/17
 * Time: 2:39 PM
 */

namespace RestfulBundle\Controller\Api\V3;


use Carbon\CarbonInterval;
use CoreBundle\Entity\User;
use Firebase\JWT\JWT;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializerBuilder;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * @Route("/api/v3/")
 */
class ChatController extends FOSRestController
{

    const CHAT_USER = "CHAT_USER_ISSUE_";

    /**
     * @Security("has_role('ROLE_USER')")
     * @Rest\Get("chat",
     *     options = { "expose" = true },
     *     name="get_chat_token")
     */
    public function getChatAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $time = time();

        $token = substr(bin2hex(random_bytes(12)),10);

        /** @var CacheItemPoolInterface $cache */
        $cache = $this->get('cache.app');

        $t = $cache->getItem(ChatController::CHAT_USER . $user->getToken());
        $t->expiresAfter(new CarbonInterval(0, 0, 0, 0, 0, 0, 30));
        $t->set($token);

        $result = JWT::encode([
            'iss' => $request->getUri(),     // The issuer of the token
            'iat' => $time,                  // Issued at: time when the token was generated
            'jti' => $token,                 // Json Token Id: an unique identifier for the token
            "nbf" => $time,                  // Not before
            'exp'  => $time + 20,            // Expire
            'token' => $user->getToken()

        ],$this->container->getParameter('env(SYMFONY_SECRET)'));

        $cache->save($t);
        return $this->view(['token' => $result]);
    }

}