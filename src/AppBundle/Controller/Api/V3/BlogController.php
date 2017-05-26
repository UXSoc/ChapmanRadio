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
use CoreBundle\Entity\User;
use CoreBundle\Filter\BlogFilter;
use CoreBundle\Helper\Paginator;
use CoreBundle\Helper\RestfulJsonResponse;
use CoreBundle\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/")
 */
class BlogController extends BaseController
{

    /**
     * @param User $user
     * @return array
     */
    private function  asAuthorMapping($user)
    {
        return [
            "name" => $user->getName(),
            "createad_at" => $user->getCreatedAt()
        ];
    }

    /**
     * @Route("blog/post", options = { "expose" = true }, name="get_blog_posts", )
     * @Method({"GET"})
     */
    public  function  getBlogPostsAction(Request $request)
    {
        $response = new RestfulJsonResponse();

        /** @var BlogRepository $blogRepository */
        $blogRepository = $this->get('core.blog_repository');

        $filter = new BlogFilter($blogRepository->createQueryBuilder('p')->where(
            'p.isPinned = 1'
        ),'p');
        $filter->nameIsLike($request->get('name'));

        $pagination = new Paginator($filter->query());
        $pagination->setEntriesPerPage($request->get("entries",10),50);
        $pagination->setCurrentPage($request->get("page",0));

        $response->setData(
        $pagination->asRestfulResponse(function ($q) {
            $blogPosts = $q->getResult();
            $result = array();

            /** @var Blog $post */
            foreach ($blogPosts as $post) {
                $result[] = [
                    "name" => $post->getName(),
                    "created_at" => $post->getCreatedAt(),
                    "updated_at" => $post->getUpdatedAt(),
                    "excerpt" => $post->getPostExcerpt(),
                    "pinned" => $post->getIsPinned()
                ];
                if($post->getAuthor())
                    $result["author"] =  $this->asAuthorMapping($post->getAuthor());
            }
            return $result;

        }));
        $response->setMessage("Query Accepted");
        return $response;

    }

    /**
     * @Route("blog/post/{name}", options = { "expose" = true }, name="get_blog_post", )
     * @Method({"GET"})
     */
    public  function  getBlogPostAction(Request $request,$name)
    {
        /** @var BlogRepository $blogRepository */
        $blogRepository = $this->get('core.blog_repository');

        /** @var Blog $post */
        $post = $blogRepository->findOneBy(['name' => $name]);
        $restful = new RestfulJsonResponse();

        if($post == null)
        {
            $restful->setMessage("Blog Post Not Found");
            $restful->setStatusCode(410);
            return $restful;
        }

        $restful->setData([
            'name' => $post->getName(),
            "created_at" => $post->getCreatedAt(),
            "updated_at" => $post->getUpdatedAt()
        ]);
        if($post->getAuthor())
            $result["author"] =  $this->asAuthorMapping($post->getAuthor());
        return $restful;

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("blog/post/{name}/comment/{comment_id}",
     *     options = { "expose" = true },
     *     name="post_show_comment",
     *     requirements={"coment_id" = "\d+"},
     *     defaults={"coment_id" = null})
     * @Method({"POST"})
     */
    public function postBlogPostCommentAction(Request $request,$name,$commentId){

    }

    /**
     * @Route("blog/post/{name}/comment/{commentId}", options = { "expose" = true }, name="shows")
     * @Method({"GET"})
     */
    public function getBlogPostCommentAction(Request $request,$name,$commentId){
        /** @var BlogRepository $blogRepository */
        $blogRepository = $this->get('core.blog_repository');

        /** @var Blog $post */
        $post = $blogRepository->findOneBy(['name' => $name]);
        $restful = new RestfulJsonResponse();

        if($post == null)
        {
            $restful->setMessage("Blog Post Not Found");
            $restful->setStatusCode(410);
            return $restful;
        }
        $restful->setData($this->commentWalk($blogRepository->getAllCommentsByParent($post)));

        $restful->setMessage("Comments found for post");
        return $restful;

    }
    private function commentWalk($comments)
    {
        $result = array();
        /** @var Comment $comment */
        foreach ($comments as $comment)
        {
            $result[] = [
                "id" => $comment->getId(),
                "content" => $comment->getContent(),
                "created_at" => $comment->getCreateAt(),
                "updated_at" => $comment->getUpdateAt(),
                "result" => $this->commentWalk($comment->getChildrenComments())
            ];

        }
        return $result;
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("blog/post/{name}/comment/{commendId}", options = { "expose" = true }, name="shows")
     * @Method({"PATCH"})
     */
    public function patchBlogPostCommentAction(Request $request,$name,$commentId){

    }


}