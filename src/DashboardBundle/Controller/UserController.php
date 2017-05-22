<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/19/17
 * Time: 7:18 PM
 */

namespace DashboardBundle\Controller;


use CoreBundle\Controller\BaseController;
use CoreBundle\Helper\DataTable;
use CoreBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    /**
     * @Route("/dashboard/users", name="dashboard_users")
     */
    public  function  indexAction(Request $request)
    {
        return $this->render('dashboard/user/users.html.twig');
    }

    /**
     * @Route("/dashboard/users/ajax/datatable",options = { "expose" = true }, name="dashboard_users_ajax_datatable")
     */
    public function userDataAction(Request $request)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get('user_repository');

        $query = $userRepository->createQueryBuilder('u');
        $parameters  = $this->getJsonPayload();

        $dataTable = new DataTable($userRepository->createQueryBuilder('u'));
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
              'name' => $val->getName(),
              'roles' => $val->getRoles()
            ];
        }

        $response->setData(array(
             'perPage' => $dataTable->getPerPage(),
             'count' => $dataTable->count(),
            'result' => $result));
        return $response;
    }

    /**
     * @Route("/dashboard/user/new-user", name="dashboard_new_user")
     */
    public  function  newUserAction(Request $request)
    {
        return $this->render('dashboard/user/');
    }


    /**
     * @Route("/dashboard/user/{id}",options = { "expose" = true },  name="dashboard_user")
     */
    public  function  userAction(Request $request,$id)
    {
        return $this->render('dashboard/shows/new-show.html.twig');
    }

}