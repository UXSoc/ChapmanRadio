<?php
namespace RestfulBundle\Controller\Api\V3;


use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Event;
use CoreBundle\Entity\Show;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\EventNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Repository\EventRepository;
use CoreBundle\Repository\ShowRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v3/")
 */
class ShowController extends Controller
{
    /**
     * @Route("show",
     *     options = { "expose" = true },
     *     name="get_shows")
     * @Method({"GET"})
     */
    public function getShowsAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);
        $pagination = $showRepository->paginator($showRepository->filter($request),
            $request->get('page',0),
            $request->get('entries',10),20);

        return RestfulEnvelope::successResponseTemplate(null,$pagination,[new ShowNormalizer(),new PaginatorNormalizer()])->response();
    }



    /**
     * @Route("show/{token}/{slug}", options = { "expose" = true }, name="get_show")
     * @Method({"GET"})
     */
    public function getShowAction(Request $request,$token,$slug){
        $em = $this->getDoctrine()->getManager();
        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        if($show = $showRepository->getShowByTokenAndSlug($token,$slug))
            return RestfulEnvelope::successResponseTemplate(null,$show,[new ShowNormalizer(),new PaginatorNormalizer()])->response();
        return RestfulEnvelope::errorResponseTemplate('Show not found')->setStatus(410)->response();
    }

    /**
     * @Route("show/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="get_show_comment")
     * @Method({"GET"})
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
                return RestfulEnvelope::successResponseTemplate('Comments',
                    $commentRepository->getCommentByShowAndToken($show,$comment_token),
                    [new UserNormalizer(), new CommentNormalizer()])->response();
            }
            else
            {
                return RestfulEnvelope::successResponseTemplate('Comments',
                    $commentRepository->getAllRootCommentsForShow($show),
                    [new UserNormalizer(), new CommentNormalizer()])->response();
            }
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->response();

    }

    /**
     * @Route("show/active",
     *     options = { "expose" = true },
     *     name="get_show_active")
     * @Method({"GET"})
     */
    public function getActiveShowAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EventRepository $eventRepository */
        $eventRepository = $em->getRepository(Event::class);
        $activeEvent = $eventRepository->getEventByTime(new \DateTime('now'));
        return $this->restful([new WrapperNormalizer(), new EventNormalizer()],new SuccessWrapper($activeEvent,'active event'));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("show/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="post_show_comment")
     * @Method({"POST"})
     */
    public function postPostCommentAction(Request $request, $token, $slug, $comment_token = null)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);
        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token,$slug))
        {
            $comment = new Comment();
            $comment->setContent($request->get("content"));
            $comment->setUser($this->getUser());

            if ($comment_token !== null) {
                if($c = $commentRepository->getCommentByShowAndToken($show, $comment_token))
                    $comment->setParentComment($c);
                else
                    return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->response();
            }

            $errors = $validator->validate($comment);
            if($errors->count() > 0)
                return RestfulEnvelope::errorResponseTemplate("invalid Comment")->addErrors($errors)->response();

            $em->persist($comment);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Comment Added',
                $comment,[new UserNormalizer(),new CommentNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->response();
    }


}
