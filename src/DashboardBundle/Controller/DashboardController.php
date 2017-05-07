<?php
namespace DashboardBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/6/17
 * Time: 11:27 PM
 */
class DashboardController extends Controller
{

    /**
     * @Route("/dashboard", name="dashboard_index")
     */
    public  function  indexAction(Request $request)
    {
        return $this->render('dashboard/dashboard.html.twig');
    }
}