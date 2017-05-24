<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace DashboardBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BlogController extends Controller
{
    /**
     * @Route("/dashboard/post/", name="dashboard_posts")
     */
    public  function  indexAction(Request $request)
    {

    }

    /**
     * @Route("/dashboard/post/new-post", name="dashboard_new_post")
     */
    public  function  newShowAction(Request $request)
    {
        return $this->render('dashboard/shows/new-show.html.twig');
    }

    /**
     * @Route("/dashboard/post/post-categories", name="dashboard_post_categories")
     */
    public  function  showCategoryAction(Request $request)
    {

    }

    /**
     * @Route("/dashboard/shows/post-tags", name="dashboard_post_tags")
     */
    public  function  showTagsAction(Request $request)
    {

    }
}