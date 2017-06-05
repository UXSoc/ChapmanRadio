<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 8:54 AM
 */

namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\Event;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\Stream;
use CoreBundle\Repository\EventRepository;
use CoreBundle\Repository\ShowRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/private")
 */
class StreamController extends Controller
{
    /**
     * @Route("stream/publish/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="post_publish_stream")
     * @Method({"POST"})
     */
    public function publishStreamAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var EventRepository $eventRepository */
        $eventRepository = $em->getRepository(Event::class);

        /** @var Show $show */
        if ($show = $showRepository->getPostByTokenAndSlug($token, $slug))
        {
            /** @var Event $event */
            $event = $eventRepository->getEventByTime(new \DateTime('now'));

            if ($event->getShow()->getId() == $show->getId())
            {

            }
                //return $this->messageError("Show Not Bound to Event", 410);

        }
           // return $this->messageError("Show Not Found", 410);

       // if ($event == null)
       //     return $this->messageError("No Shows Scheduled", 410);


    }
}

