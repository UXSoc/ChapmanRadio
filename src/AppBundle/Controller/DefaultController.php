<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller;

use CoreBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/23/17
 * Time: 10:53 AM
 */
class DefaultController extends BaseController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
}