<?php
namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Event;
use CoreBundle\Entity\Image;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\Tag;
use CoreBundle\Form\CommentType;
use CoreBundle\Form\ShowType;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Repository\EventRepository;
use CoreBundle\Repository\ShowRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\ShowVoter;
use CoreBundle\Service\ImageUploadService;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/v3/")
 */
class ShowController extends FOSRestController
{
    /**
     * @Rest\Get("show",
     *     options = { "expose" = true },
     *     name="get_shows")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getShowsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        return $this->view(['payload' =>
            $showRepository->paginator($showRepository->filter($request),
                (int)$request->get('page', 0),
                (int)$request->get('perPage', 10), 20)]);
    }



    /**
     * @Rest\Get("show/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="get_show")
     * @Rest\View(serializerGroups={"detail"})
     */
    public function getShowAction(Request $request,$token,$slug){
        $em = $this->getDoctrine()->getManager();
        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        if($show = $showRepository->getShowByTokenAndSlug($token,$slug)) {
            $show->setDeltaRenderer($request->get('delta','HTML'));
            return $this->view(['show' => $show]);
        }
        throw $this->createNotFoundException("Show Not Found");
    }

    /**
     * @Rest\Get("show/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="get_show_comment")
     */
    public function getShowCommentAction(Request $request,$token,$slug,$comment_token = null){
        $em = $this->getDoctrine()->getManager();

        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        if( $show = $showRepository->getShowByTokenAndSlug($token,$slug)) {
            if ($comment_token) {
                return $this->view(["comments" => $commentRepository->getCommentByShowAndToken($show, $comment_token)]);
            } else {
                return $this->view(["comments" => $commentRepository->getAllRootCommentsForShow($show)]);
            }
        }
        throw $this->createNotFoundException('Show not Found');
    }

    /**
     * @Rest\Get("show/active",
     *     options = { "expose" = true },
     *     name="get_show_active")
     */
    public function getActiveShowAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EventRepository $eventRepository */
        $eventRepository = $em->getRepository(Event::class);
        $activeEvent = $eventRepository->getEventByTime(new \DateTime('now'));
        return $this->view(["event" => $activeEvent]);
    }

}
