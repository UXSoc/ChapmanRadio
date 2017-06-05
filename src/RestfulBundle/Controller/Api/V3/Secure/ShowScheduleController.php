<?php

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Event;
use CoreBundle\Entity\Show;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\EventNormalizer;
use CoreBundle\Repository\ShowRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/private")
 */
class ShowScheduleController extends Controller
{
    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/show/{token}/{slug}/schedule/event/recurring", options = { "expose" = true }, name="post_schedule_recurring_show")
     * @Method({"POST"})
     */
    public function postScheduleRecurringShowAction(Request $request, $token, $slug)
    {
        $request->get('weekly', null);
        $request->get('month', null);

    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/show/{token}/{slug}/schedule/event",
     *     options = { "expose" = true },
     *     name="post_show_schedule_event")
     * @Method({"PUT"})
     */
    public function postScheduleEventAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        if($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $event = new Event();
            $event->setStart(new \DateTime($request->get("start", null)));
            $event->setEnd(new \DateTime( $request->get("end", null)));
            $em->persist($event);

            $show->addEvent($event);
            $em->persist($show);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Event added',$event,[new EventNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Show not found')->response();

    }
}
