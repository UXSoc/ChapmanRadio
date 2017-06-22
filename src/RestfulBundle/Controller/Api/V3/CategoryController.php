<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:43 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Category;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\CategoryNormalizer;
use CoreBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v3/")
 */
class CategoryController extends Controller
{
    /**
     * @Route("category",
     *     options = { "expose" = true },
     *     name="get_categories")
     * @Method({"GET"})
     */
    public function getCategoriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);

        return RestfulEnvelope::successResponseTemplate(
            null, $categoryRepository->findCategory($request->get('search', '')), [new CategoryNormalizer()])->response();

    }

    /**
     * @Route("category/{name}",
     *     options = { "expose" = true },
     *     name="get_category")
     * @Method({"GET"})
     */
    public function getCategoryAction(Request $request, $name)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);

        if ($category = $categoryRepository->getCategory($name))
            return RestfulEnvelope::successResponseTemplate(
                null,$category,[new CategoryNormalizer()])->response();
        return RestfulEnvelope::errorResponseTemplate("Can't find Category")->setStatus(410)->response();

    }

}
