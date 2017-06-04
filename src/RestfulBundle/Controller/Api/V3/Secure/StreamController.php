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
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/private")
 */
class StreamController extends BaseController
{
    /**
     * @Route("stream/publish/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="post_publish_stream")
     * @Method({"POST"})
     */
    public function PublishStreamAction(Request $request,$token,$slug)
    {
        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        /** @var EventRepository $eventRepository */
        $eventRepository = $this->get('core.event_repository');

        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token,$slug);
        if ($show == null)
            return $this->messageError("Show Not Found",410);
        /** @var Event $event */
        $event =  $eventRepository->getEventByTime(new \DateTime('now'));
        if($event == null)
            return $this->messageError("No Shows Scheduled",410);

        if($event->getShow() != $show)
            return $this->messageError("Show Not Bound to Event",410);



    }
}