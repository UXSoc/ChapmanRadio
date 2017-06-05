<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:48 PM
 */

namespace RestfulBundle\Controller\Api\V3\Secure;
use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\Category;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\CategoryNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v3/private")
 */
class CategoryController extends BaseController
{
    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/category/{category}", options = { "expose" = true }, name="put_category")
     * @Method({"PUT"})
     */
    public function putCategoryAction($category)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);
        $result = $categoryRepository->getCategory($category);
        if($result  != null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Category Already Exist"), 410);

        $result = new Category();
        $result->setCategory($category);

        $em->persist($result);
        $em->flush();
        return $this->restful([new WrapperNormalizer(),new CategoryNormalizer()],new SuccessWrapper($result,"Tag added"));
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/category/{category}", options = { "expose" = true }, name="delete_category")
     * @Method({"DELETE"})
     */
    public function deleteCategoryAction($category)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);
        $result = $categoryRepository->getCategory($category);
        if($result  == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Category does not exist"), 410);

        $em->remove($result);
        $em->flush();
        return $this->restful([new WrapperNormalizer(),new CategoryNormalizer()],new SuccessWrapper($result,"Tag was Removed"));
    }

}
