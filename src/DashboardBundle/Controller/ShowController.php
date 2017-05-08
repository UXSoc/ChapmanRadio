<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/7/17
 * Time: 3:57 PM
 */

namespace DashboardBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ShowController extends Controller
{
    /**
     * @Route("/dashboard/shows", name="dashboard_shows")
     */
    public  function  indexAction(Request $request)
    {

    }

    /**
     * @Route("/dashboard/new-show", name="dashboard_new_show")
     */
    public  function  newShowAction(Request $request)
    {
        return $this->render('dashboard/shows/new-show.html.twig');
    }

    /**
     * @Route("/dashboard/shows-categories", name="dashboard_show_categories")
     */
    public  function  showCategoryAction(Request $request)
    {

    }

    /**
     * @Route("/dashboard/show-tags", name="dashboard_show_tags")
     */
    public  function  showTagsAction(Request $request)
    {

    }

}