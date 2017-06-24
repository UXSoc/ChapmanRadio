<?php
namespace RestfulBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController extends FOSRestController
{
    /**
     * @Rest\Get("/", name="index")
     */
    public function indexAction(Request $request)
    {
        return $this->handleView($this->view()
            ->setTemplate('default/index.html.twig')
            ->setTemplateData(['base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR]));

    }


    /**
     * @Route("/stream", name="get_stream")
     */
    public function testStreamAction(Request $request)
    {
        return new JsonResponse([]);
    }
}
