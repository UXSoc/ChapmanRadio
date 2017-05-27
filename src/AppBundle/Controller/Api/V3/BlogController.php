<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/24/17
 * Time: 10:46 PM
 */

namespace AppBundle\Controller\Api\V3;


use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Blog;
use CoreBundle\Entity\Comment;
use CoreBundle\Helper\RestfulJsonResponse;
use CoreBundle\Normalizer\BlogNormalizer;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Repository\BlogRepository;
use CoreBundle\Repository\CommentRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/api/v3/")
 */
class BlogController extends BaseController
{

    /**
     * @Route("post", options = { "expose" = true }, name="get_blog_posts", )
     * @Method({"GET"})
     */
    public  function  getBlogPostsAction(Request $request)
    {
        /** @var BlogRepository $blogRepository */
        $blogRepository = $this->get('core.blog_repository');

        $q = $blogRepository->createQueryBuilder('p');

        $name = $request->get('name',null);
        if($name)
            $q->where($q->expr()->like('p.name',':name'))
              ->setParameter('name','%'.$request->get('name').'%');

        $pagination = new Paginator($q->getQuery());
        $perPage = $request->get("entries",10) > 20 ? 20 :  $request->get("entries",20);
        $pagination->getQuery()->setMaxResults($perPage);
        $pagination->getQuery()->setFirstResult($perPage * $request->get("page",0));

        $restfulResponse = new RestfulJsonResponse();
        $restfulResponse->setMessage("Query Accepted");
        $restfulResponse->normalize(array(
            new BlogNormalizer(),
            new UserNormalizer(),
            new PaginatorNormalizer()),$pagination);
        return $restfulResponse;

    }

    /**
     * @Route("post/{token}/{slug}", options = { "expose" = true }, name="get_blog_post", )
     * @Method({"GET"})
     */
    public  function  getBlogPostAction(Request $request,$token,$slug)
    {
        /** @var BlogRepository $blogRepository */
        $blogRepository = $this->get('core.blog_repository');

        /** @var Blog $post */
        $post = $blogRepository->findOneBy(['token' => $token,'slug' => $slug]);
        $restful = new RestfulJsonResponse();

        if($post == null)
        {
            $restful->setMessage("Blog Post Not Found");
            $restful->setStatusCode(410);
            return $restful;
        }
        $restful->normalize(array(
            new BlogNormalizer(),
            new UserNormalizer(),
            new PaginatorNormalizer()
        ),$post,null);
        return $restful;

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("post/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="post_show_comment")
     * @Method({"POST"})
     */
    public function postBlogPostCommentAction(Request $request,$token,$slug,$comment_token){
        /** @var BlogRepository $blogRepository */
        $blogRepository = $this->get('core.blog_repository');

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.comment_repository');

        /** @var Blog $post */
        $post = $blogRepository->findOneBy(['token' => $token,'slug' => $slug]);

        $restful = new RestfulJsonResponse();
        if($post == null)
        {
            $restful->setMessage("Blog Post Not Found");
            $restful->setStatusCode(410);
            return $restful;
        }

        if($comment_token == null)
        {
            $comment = new Comment();

        }
        else
        {

        }
    }

    /**
     * @Route("post/{token}/{slug}/comment/{comment_token}", options = { "expose" = true }, name="get_blog_comment")
     * @Method({"GET"})
     */
    public function getBlogPostCommentAction(Request $request,$token,$slug,$comment_token = null){
        /** @var BlogRepository $blogRepository */
        $blogRepository = $this->get('core.blog_repository');

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.comment_repository');

        /** @var Blog $post */
        $post = $blogRepository->findOneBy(['token' => $token,'slug' => $slug]);
        $restful = new RestfulJsonResponse();

        if($post == null)
        {
            $restful->setMessage("Blog Post Not Found");
            $restful->setStatusCode(410);
            return $restful;
        }

        $restful->normalize(array(
            new CommentNormalizer(),
            new UserNormalizer()
        ),$commentRepository->getAllRootCommentsForBlogEntiry($post)->getQuery()->getResult());

        $restful->setMessage("Comments found for post");
        return $restful;

    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("post/{name}/comment/{commendId}", options = { "expose" = true }, name="shows")
     * @Method({"PATCH"})
     */
    public function patchBlogPostCommentAction(Request $request,$name,$commentId){

    }


}