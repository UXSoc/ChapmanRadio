<?php
namespace AppBundle\Controller\Api\V3;

// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Show;
use CoreBundle\Helper\RestfulJsonResponse;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Repository\ShowRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Proxies\__CG__\CoreBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/")
 */
class ShowController extends BaseController
{
    /**
     * @Route("show", options = { "expose" = true }, name="get_shows")
     * @Method({"GET"})
     */
    public function getShowsAction(Request $request){
        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');
        $qb = $showRepository->createQueryBuilder('s');

        $name = $request->get('name',null);
        if($name)
            $qb->where($qb->expr()->like('name',':name'))
                ->setParameter('name','%' .$name.'%');

        $pagination = new Paginator($qb->getQuery());
        $perPage = $request->get("entries",10) > 20 ? 20 :  $request->get("entries",20);
        $pagination->getQuery()->setMaxResults($perPage);
        $pagination->getQuery()->setFirstResult($perPage * $request->get("page",0));

        $restfulResponse = new RestfulJsonResponse();
        $restfulResponse->setMessage("Query Accepted");
        $restfulResponse->normalize(array(
            new ShowNormalizer(),
            new PaginatorNormalizer()
        ),$pagination);
        return $restfulResponse;
    }

    /**
     * @Route("show/{token}/{slug}", options = { "expose" = true }, name="get_show")
     * @Method({"GET"})
     */
    public function getShowAction(Request $request,$token,$slug){
        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        $show = $showRepository->findOneBy(['token' => $token,'slug' => $slug]);
        $restful = new RestfulJsonResponse();

        if($show == null)
        {
            $restful->setMessage("Show Not Found");
            $restful->setStatusCode(410);
            return $restful;
        }

        $restful->normalize(array(
            new ShowNormalizer()
        ),$show,null);
        return $restful;
    }



    /**
     * @Route("show/{token}/{slug}/comment/{comment_token}", options = { "expose" = true }, name="get_show_comment")
     * @Method({"GET"})
     */
    public function getShowCommentAction(Request $request,$token,$slug,$comment_token = null){
        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.comment_repository');

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        $show = $showRepository->findOneBy(['token' => $token,'slug' => $slug]);
        $restful = new RestfulJsonResponse();

        if($show == null)
        {
            $restful->setMessage("Show Not Found");
            $restful->setStatusCode(410);
            return $restful;
        }

        if($show->getEnableComments() == false)
        {
            $restful->setMessage("Comments Disabled For Show");
            $restful->setStatusCode(400);
            return $restful;
        }

        $restful->normalize(array(
            new CommentNormalizer(),
            new UserNormalizer()
        ),$commentRepository->getAllRootCommentsForShowEntiry($show));

        $restful->setMessage("Comments found for Show");
        return $restful;

    }
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("show/{name}/comment/{commentId}", options = { "expose" = true }, name="post_show_comment")
     * @Method({"POST"})
     */
    public function postShowCommentAction(Request $request,$name,$commentId = null){
        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.show_repository');

        $restful = new RestfulJsonResponse();

        /** @var Show $show */
        $show = $showRepository->findOneBy(['name' => $name]);
        if($show == null)
        {
            $restful->setMessage("Show Not Found");
            $restful->setStatusCode(410);
            return $restful;
        }

        $body = $request->get("body");
        $restful = new RestfulJsonResponse();
        if($body == null)
        {
            $restful->setMessage("Comment needs a body");
            $restful->setStatusCode(400);
            return $restful;
        }

        $parentComment = null;
        if($commentId != null) {
            $parentComment = $showRepository->getComment($show, $commentId);
            if($parentComment == null)
            {
                $restful->setMessage("Unknown comment");
                $restful->setStatusCode(400);
                return $restful;
            }
        }
        $em = $this->getDoctrine()->getManager();

        //create comment
        $comment = new Comment();
        $comment->setContent($body);
        $comment->setUser($this->getUser());
        $comment->setParentComment($parentComment);

        $em->persist($comment);
        //add comment to show and persist
        $show->addComment($comment);
        $em->persist($show);
        $em->flush();

        $restful->normalize(array(
            new CommentNormalizer(),
            new UserNormalizer()
        ),$comment);
        return $restful;

    }
//
//    /**
//     * @Route("show/{name}/comment/{commentId}", options = { "expose" = true }, name="patch_comment")
//     * @Method({"PATCH"})
//     */
//    public function patchShowCommentAction($name,$commentId = null){
//
//        /** @var ShowRepository $blogRepository */
//        $showRepository = $this->get('core.show_repository');
//        $restful = new RestfulJsonResponse();
//
//        /** @var Show $show */
//        $show = $showRepository->findOneBy(['name' => $name]);
//        if($show == null)
//        {
//            $restful->setMessage("Show Not Found");
//            $restful->setStatusCode(410);
//            return $restful;
//        }
//
//        $parentComment = $showRepository->getComment($show, $commentId);
//
//    }

}