<?php
namespace RestfulBundle\Controller\Api\V3;

use BroadcastBundle\Entity\Stream;
use CoreBundle\Entity\Event;
use CoreBundle\Entity\Show;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\EventNormalizer;
use CoreBundle\Normalizer\StreamNormalizer;
use CoreBundle\Repository\EventRepository;
use CoreBundle\Repository\ShowRepository;
use CoreBundle\Repository\StreamRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/v3/")
 */
class StreamController extends FOSRestController
{
    /**
     * @Rest\Get("stream",
     *     options = { "expose" = true },
     *     name="get_active_streams")
     * @Method({"GET"})
     */
    public function getAllActiveStreamsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var StreamRepository $streamRepository */
        $streamRepository =  $em->getRepository(Stream::class);
        $streams =  $streamRepository->findAll();

        return $this->view(["streams" => $streams]);
    }

    /**
     * @param Request $request
     * @Rest\Get("stream/main",
     *     options = { "expose" = true },
     *     name="get_main_streams")
     */
    public function getMainStreamAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var StreamRepository $streamRepository */
        $streamRepository =  $em->getRepository(Stream::class);
        $streams = $streamRepository->getStreamsTiedToEvent();

        return $this->view(["streams" => $streams]);
    }

    /**
     * @Rest\Post("stream/publish/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="post_publish_stream")
     */
    public function publishStreamAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var EventRepository $eventRepository */
        $eventRepository = $em->getRepository(Event::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            /** @var Event $event */
            $event = $eventRepository->getEventByTime(new \DateTime('now'));

            if ($event->getShow()->getId() == $show->getId())
            {

            }
            //return $this->messageError("Show Not Bound to Event", 410);

        }


    }

}
