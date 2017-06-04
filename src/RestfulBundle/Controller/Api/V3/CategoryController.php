<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:43 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Controller\BaseController;

use CoreBundle\Helper\ErrorWrapper;
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
     * @Route("tag",
     *     options = { "expose" = true },
     *     name="get_tags")
     * @Method({"GET"})
     */
    public function getCategoriesAction(Request $request)
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->get('core.category_repository');
        return $this->restful([new WrapperNormalizer(),
            new CategoryNormalizer()], new SuccessWrapper($categoryRepository->findAll(), null));
    }

    /**
     * @Route("tag/{name}",
     *     options = { "expose" = true },
     *     name="get_tag")
     * @Method({"GET"})
     */
    public function getCategoryAction(Request $request, $name)
    {
        /** @var RestfulService $restfulService */
        $restfulService = $this->get('core.restful');


        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->get('core.category_repository');

        $category = $categoryRepository->findOneBy(["tag" => $name]);
        if ($category === null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Can't find tag"), 400);

        return $restfulService->successResponse([new CategoryNormalizer()],$category,"Found Tag");
    }

}
