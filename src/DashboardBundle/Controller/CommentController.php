<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
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