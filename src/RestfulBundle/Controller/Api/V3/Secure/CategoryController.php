<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:48 PM
 */

namespace RestfulBundle\Controller\Api\V3\Secure;
use CoreBundle\Entity\Category;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\CategoryNormalizer;
use CoreBundle\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/v3/private")
 */
class CategoryController extends Controller
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

        if($result = $categoryRepository->getCategory($category))
            return RestfulEnvelope::errorResponseTemplate('Category found')->setStatus(400)->response();

        $c = new Category();
        $c->setCategory($category);
        $em->persist($c);
        $em->flush();
        return RestfulEnvelope::successResponseTemplate('Category added', $c,
            [new CategoryNormalizer()])->response();
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
        if( $result = $categoryRepository->getCategory($category))
        {
            $em->remove($result);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Category deleted', $result,
                [new CategoryNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Category not found')->response();
    }

}
