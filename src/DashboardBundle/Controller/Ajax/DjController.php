<?php namespace DashboardBundle\Controller\Ajax;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/18/17
 * Time: 2:32 PM
 */
class DjController extends Controller
{
    /**
     * @Route("/dashboard/ajax/dj", name="dashboard_ajax_dj_name_like")
     */
    public  function  ajaxNameLikeAction(Request $request)
    {

    }
}