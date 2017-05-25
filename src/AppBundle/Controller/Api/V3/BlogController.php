<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/24/17
 * Time: 10:46 PM
 */

namespace AppBundle\Controller\Api\V3;


use CoreBundle\Controller\BaseController;

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
     * @Route("blog/post", options = { "expose" = true }, name="get_blog_posts", )
     * @Method({"GET"})
     */
    public  function  getBlogPostsAction(Request $request)
    {
        $request->get("search");
    }

    /**
     * @Route("blog/post/{name}", options = { "expose" = true }, name="get_blog_post", )
     * @Method({"GET"})
     */
    public  function  getBlogPostAction(Request $request)
    {

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("blog/post/{name}/comment/{comment_id}",
     *     options = { "expose" = true },
     *     name="post_show_comment",
     *     requirements={"coment_id" = "\d+"},
     *     defaults={"coment_id" = 1})
     * @Method({"POST"})
     */
    public function postBlogPostCommentAction(Request $request){

    }

    /**
     * @Route("blog/post/{name}/comment", options = { "expose" = true }, name="shows")
     * @Method({"GET"})
     */
    public function getBlogPostCommentAction(Request $request){

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("blog/post/{name}/comment", options = { "expose" = true }, name="shows")
     * @Method({"PATCH"})
     */
    public function patchBlogPostCommentAction(Request $request){

    }


}