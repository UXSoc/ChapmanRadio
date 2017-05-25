<?php
namespace AppBundle\Controller\Api\V3;

// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
use CoreBundle\Controller\BaseController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v3/")
 */
class ShowController extends BaseController
{
    /**
     * @Route("show", options = { "expose" = true }, name="get_shows")
     * @Method({"GET"})
     */
    public function getShowsAction(){

    }

    /**
     * @Route("show/{name}", options = { "expose" = true }, name="get_show")
     * @Method({"GET"})
     */
    public function getShowAction(){

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("show/{name}/comment/{comment_id}", options = { "expose" = true }, name="post_show_comment")
     * @Method({"POST"})
     */
    public function postShowCommentAction(){

    }

    /**
     * @Route("show/{name}/comment/{comment_id}", options = { "expose" = true }, name="get_show_comment")
     * @Method({"GET"})
     */
    public function getShowCommentAction(){

    }

    /**
     * @Route("show/{name}/comment/{comment_id}", options = { "expose" = true }, name="patch_comment")
     * @Method({"PATCH"})
     */
    public function patchShowCommentAction(){

    }

}