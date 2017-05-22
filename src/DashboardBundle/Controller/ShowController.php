<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/7/17
 * Time: 3:57 PM
 */

namespace DashboardBundle\Controller;


use CoreBundle\Controller\BaseController;
use CoreBundle\Helper\DataTable;
use CoreBundle\Repository\ShowRepository;
use DashboardBundle\Form\ShowType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ShowController extends BaseController
{
    /**
     * @Route("/dashboard/show", name="dashboard_shows")
     */
    public  function  indexAction(Request $request)
    {
        return $this->render('dashboard/shows/shows.html.twig');
    }

    /**
     * @Route("/dashboard/show/{id}",options = { "expose" = true }, name="dashboard_show")
     * @param Request $request
     */
    public function showAction(Request $request)
    {
        /** @var $form Form*/
        $showForm = $this->createForm(ShowType::class);

        $showForm->handleRequest($request);
        if ($showForm->isSubmitted() && $showForm->isValid()) {

        }

        return $this->render('dashboard/shows/show.html.twig',['show_form' => $showForm->createView()]);
    }
    /**
     * @Route("/dashboard/show/ajax/datatable",options = { "expose" = true }, name="dashboard_show_ajax_datatable")
     * @param Request $request
     */
    public function showDataAction(Request $request)
    {
        /** @var ShowRepository $userRepository */
        $showRepository = $this->get('core.show_repository');


        $parameters  = $this->getJsonPayload();

        $dataTable = new DataTable($showRepository->createQueryBuilder('u'));
        $dataTable->setAlias([
            'name' => 'u.name',
            'id' => 'u.id'
        ])->parseSort(['id','name'],$parameters->get('sort',array()))
            ->setCurrentPage($parameters->get("currentPage",0))
            ->setPerPage($parameters->get("perPage",10))
            ->setIndex('u.id');

        $response = new JsonResponse();

        $result = [];
        foreach ($dataTable->getIterator() as $val)
        {
            $result[] = [
                'id' => $val->getId(),
                'name' => $val->getName()
            ];
        }

        $response->setData(array(
            'perPage' => $dataTable->getPerPage(),
            'count' => $dataTable->count(),
            'result' => $result));
        return $response;

    }

    /**
     * @Route("/dashboard/show/new-show", name="dashboard_new_show")
     */
    public  function  newShowAction(Request $request)
    {
        return $this->render('dashboard/shows/new-show.html.twig');
    }

    /**
     * @Route("/dashboard/show/shows-categories", name="dashboard_show_categories")
     */
    public  function  showCategoryAction(Request $request)
    {
        return $this->render('dashboard/shows/categories.html.twig');
    }

    /**
     * @Route("/dashboard/show/show-tags", name="dashboard_show_tags")
     */
    public  function  showTagsAction(Request $request)
    {
        return $this->render('dashboard/shows/tags.html.twig');
    }



}