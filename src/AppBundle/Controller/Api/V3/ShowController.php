<?php
namespace AppBundle\Controller\Api\V3;

// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Show;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Repository\ShowRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

        return $this->restful([
            new ShowNormalizer(),
            new PaginatorNormalizer(),
            new WrapperNormalizer()
        ],new SuccessWrapper($pagination));
    }

    /**
     * @Route("show/{token}/{slug}", options = { "expose" = true }, name="get_show")
     * @Method({"GET"})
     */
    public function getShowAction(Request $request,$token,$slug){
        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        $show = $showRepository->findOneBy(['token' => $token,'slug' => $slug]);

        if($show == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Show Not Found"),410);

        return $this->restful([
            new ShowNormalizer()
        ],$show);
    }

    /**
     * @Route("show/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="get_show_comment")
     * @Method({"GET"})
     */
    public function getShowCommentAction(Request $request,$token,$slug,$comment_token = null){
        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.comment_repository');

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        /** @var Show $show */
        $show = $showRepository->findOneBy(['token' => $token,'slug' => $slug]);

        if($show == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        if($comment_token == null) {
            return $this->restful([new CommentNormalizer(),
                new UserNormalizer(),
                new WrapperNormalizer()],
                new SuccessWrapper($commentRepository->getAllRootCommentsForShow($show)));
        }
        else
        {
            try {
                $comment = $commentRepository->getCommentByShowAndToken($show, $comment_token);
                return $this->restful([new CommentNormalizer(),
                    new UserNormalizer(),
                    new WrapperNormalizer()],
                    new SuccessWrapper($comment));

            } catch (NoResultException $e) {
                return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Unknown Comment"), 410);
            }

        }

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("show/{token}/{slug}/comment/{comment_token}", options = { "expose" = true }, name="post_show_comment")
     * @Method({"POST"})
     */
    public function postShowCommentAction(Request $request,$token,$slug,$comment_token = null){
        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.show_repository');

        /** @var Show $show */
        $show = $showRepository->findOneBy(['token' => $token,'slug' => $slug]);

        if($show == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Show Not Found"),410);

        $comment = new Comment();
        $comment->setContent($request->get("content"));
        $comment->setUser($this->getUser());

        if($comment_token != null) {
            try {
                $comment->setParentComment($commentRepository->getCommentByShowAndToken($show, $comment_token));
            } catch (NoResultException $e) {
                return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Unknown Comment"), 410);
            }
        }

        $errors = $this->validateEntity($comment);
        if($errors->count() > 0)
        {
            $error = new ErrorWrapper("invalid token");
            $error->addErrors($this->validateEntity($comment));
            $error->setMessage("Invalid Comment");
            return $this->restful([new WrapperNormalizer()],$error,400);
        }

        return $this->restful([
            new WrapperNormalizer(),
            new CommentNormalizer(),
            new UserNormalizer()
        ],new SuccessWrapper($comment,"Comment Added"));
    }

}