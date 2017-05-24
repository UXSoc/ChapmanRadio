<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class ProfileController extends Controller
{
    /**
     * @Route("/dashboard/profile/", name="dashboard_user_profile")
     */
    public  function  profileAction(Request $request)
    {
        return $this->render('dashboard/user/profile.html.twig');
    }
}