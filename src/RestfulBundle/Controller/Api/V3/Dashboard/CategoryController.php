<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/26/17
 * Time: 8:40 PM
 */

namespace RestfulBundle\Controller\Api\V3\Dashboard;

use CoreBundle\Entity\Category;
use CoreBundle\Form\Type\CategoryType;
use CoreBundle\Repository\CategoryRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/api/v3/")
 */
class CategoryController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Put("/category/{category}",
     *     options = { "expose" = true },
     *     name="put_category")
     */
    public function putCategoryAction(Request $request,$category)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);

        if($result = $categoryRepository->getCategory($category))
            return new HttpException(409,"Category Already Exist");

        $c = new Category();
        $c->setCategory($category);
        $em->persist($c);
        $em->flush();
        return $this->view(['category' => $c->getCategory()]);
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Delete("/category/{category}",
     *     options = { "expose" = true },
     *     name="delete_category")
     */
    public function deleteCategoryAction($category)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);
        /** @var Category $result */
        if( $result = $categoryRepository->getCategory($category))
        {
            $em->remove($result);
            $em->flush();
            return $this->view(['category' => $result->getCategory()]);
        }
        return $this->createNotFoundException("Can't Find Category");
    }
}