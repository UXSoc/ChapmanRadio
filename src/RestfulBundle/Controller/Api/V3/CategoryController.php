<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:43 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Category;
use CoreBundle\Repository\CategoryRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/v3/")
 */
class CategoryController extends FOSRestController
{
    /**
     * @Rest\Get("category",
     *     options = { "expose" = true },
     *     name="get_categories")
     */
    public function getCategoriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);
        $categories = $categoryRepository->findCategory($request->get('search', ''));

        return $this->view(["categories" =>
        array_map(function ($value) {
            return $value->getCategory();
        },$categories)]);
    }

    /**
     * @Rest\Get("category/{name}",
     *     options = { "expose" = true },
     *     name="get_category")
     */
    public function getCategoryAction(Request $request, $name)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);

        /** @var Category $category */
        if ($category = $categoryRepository->getCategory($name))
            return $this->view($category->getCategory());
        return $this->createNotFoundException("Can't Find Category");
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Put("/category/{category}",
     *     options = { "expose" = true },
     *     name="put_category")
     */
    public function putCategoryAction($category)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);

        if($result = $categoryRepository->getCategory($category))
            return $this->createNotFoundException("Can't Find Category");

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
