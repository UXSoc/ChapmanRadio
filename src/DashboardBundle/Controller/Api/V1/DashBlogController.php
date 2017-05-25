<?php
namespace DashboardBundle\Controller\Api\V1;

use CoreBundle\Controller\BaseController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/api/v3/")
 */
class DashBlogController extends BaseController
{

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/post/{name}", options = { "expose" = true }, name="get_post_datatable")
     * @Method({"PATCH"})
     */
    public function getPostDatatableAction(){

    }

    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("blog/post/{name}", options = { "expose" = true }, name="patch_post")
     * @Method({"PATCH"})
     */
    public function patchPostAction(){

    }


    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("blog/post/{name}", options = { "expose" = true }, name="delete_post")
     * @Method({"PATCH"})
     */
    public function deletePostAction(){

    }

    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("blog/post", options = { "expose" = true }, name="post_post")
     * @Method({"POST"})
     */
    public function postPostAction() {

    }

    //----------------------------------------------------------------------------------------

    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("blog/post/{post}/tag/{tag}", options = { "expose" = true }, name="put_tag_post")
     * @Method({"PUT"})
     */
    public function putTagForPostAction() {

    }

    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("blog/post/{post}/tag/{tag}", options = { "expose" = true }, name="delete_tag_post")
     * @Method({"DELETE"})
     */
    public function deleteTagForPostAction() {

    }


    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("blog/post/{post}/category/{category}", options = { "expose" = true }, name="put_category_post")
     * @Method({"PUT"})
     */
    public function putCategoryForPostAction() {

    }

    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("blog/post/{post}/category/{category}", options = { "expose" = true }, name="delete_category_post")
     * @Method({"DELETE"})
     */
    public function deleteCategoryForPostAction() {

    }


    //----------------------------------------------------------------------------------------

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/tag", options = { "expose" = true }, name="put_tag")
     * @Method({"PUT"})
     */
    public function putTagAction() {

    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/tag", options = { "expose" = true }, name="delete_tag")
     * @Method({"DELETE"})
     */
    public function deleteTagAction(){

    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/tag", options = { "expose" = true }, name="put_category")
     * @Method({"PUT"})
     */
    public function putCategoryAction() {

    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/tag", options = { "expose" = true }, name="delete_category")
     * @Method({"DELETE"})
     */
    public function deleteCategoryAction(){

    }


}