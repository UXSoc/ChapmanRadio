<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/19/17
 * Time: 7:18 PM
 */

namespace DashboardBundle\Controller;


use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\User;
use CoreBundle\Helper\DataTable;
use CoreBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController
{

    //Some basic testing
    /**
     * @Route("/test", name="test")
     */
    public function test(Request $request)
    {
        $user = new User();
        return new JsonResponse($this->getErrors($user));

    }

    /**
     * @Route("/dashboard/ajax/user",options = { "expose" = true }, name="dashboard_ajax_user")
     */
    public function userDataAction(Request $request)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get('core.user_repository');

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



}