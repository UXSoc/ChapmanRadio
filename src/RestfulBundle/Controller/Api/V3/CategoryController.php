<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:43 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Category;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\CategoryNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\CategoryRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Service\RestfulService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/")
 */
class CategoryController extends BaseController
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
            null,$categoryRepository->findAll(),[new CategoryNormalizer()])->response();
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

        /** @var RestfulService $restfulService */
        $restfulService = $this->get(RestfulService::class);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);

        if ($category = $categoryRepository->getCategory($name))
            return RestfulEnvelope::successResponseTemplate(
                null,$category,[new CategoryNormalizer()])->response();
        return RestfulEnvelope::errorResponseTemplate("Can't find Category")->setStatus(410)->response();

    }

}
