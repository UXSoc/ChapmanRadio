<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/19/17
 * Time: 3:02 PM
 */

namespace DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class ProfileController extends Controller
{
    /**
     * @Route("/dashboard/user/", name="dashboard_user_profile")
     */
    public  function  profileAction(Request $request)
    {
        return $this->render('dashboard/user/profile.html.twig');
    }
}