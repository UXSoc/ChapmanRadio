<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/21/17
 * Time: 3:37 PM
 */

namespace DashboardBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CommentController  extends Controller
{

    /**
     * @Route("/comments", name="dashboard_comments")
     */
    public  function  indexAction(Request $request)
    {
        return $this->render('dashboard/dashboard.html.twig');
    }
}