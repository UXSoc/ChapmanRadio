<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/27/17
 * Time: 6:14 PM
 */

namespace RestfulBundle\Controller\Api\V3;


use CoreBundle\Entity\User;
use CoreBundle\Event\ImageEvent;
use CoreBundle\Events;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/api/v3/")
 */
class UserController extends FOSRestController
{
    /**
     * @Rest\Get("user/{token}/profile/image",
     *     options = { "expose" = true },
     *     name="get_profile_image")
     */
    public function getImage(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user =  $em->getRepository(User::class)->findOneBy(['token' => $token]);
        $image = $user->getProfile()->getImage();
        if($image === null)
        {

        }
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');
        $event = new ImageEvent($image);
        $dispatcher->dispatch(Events::IMAGE_RETRIEVE,$event);
        return new Response(file_get_contents($event->getPath()), 200,  array(
            'Content-Type' => 'image/png'
        ));

    }


}