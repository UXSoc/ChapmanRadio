<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/21/17
 * Time: 2:34 PM
 */

namespace DashboardBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SettingsController extends Controller
{
    /**
     * @Route("/dashboard/settings", name="dashboard_settings")
     */
    public  function  indexAction(Request $request)
    {

    }

}