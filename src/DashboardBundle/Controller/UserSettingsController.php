<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/18/17
 * Time: 10:16 PM
 */

namespace DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserSettingsController extends Controller
{
    /**
     * @Route("/dashboard/user/settings", name="dashboard_user_settings")
     */
    public  function  settingsAction(Request $request)
    {
        return $this->render('dashboard/user/user_settings.html.twig');
    }

    /**
     * @Route("/dashboard/user/settings", name="dashboard_user_settings")
     */
    public  function  djProfileAction(Request $request)
    {
        return $this->render('dashboard/user/user_settings.html.twig');
    }
}