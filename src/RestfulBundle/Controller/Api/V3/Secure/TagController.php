<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:47 PM
 */

namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Category;
use CoreBundle\Entity\Tag;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/v3/private")
 */
class TagController  extends Controller
{

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/tag/{tag}", options = { "expose" = true }, name="put_tag")
     * @Method({"PUT"})
     */
    public function putTagAction($tag)
    {

        $em = $this->getDoctrine()->getManager();
        /** @var TagRepository $tagRepository */
        $tagRepository = $em->getRepository(Tag::class);

        if($result = $tagRepository->getTag($tag))
            return RestfulEnvelope::errorResponseTemplate('Tag duplicate')->response();

        $c = new Tag();
        $c->setTag($tag);
        $em->persist($c);
        $em->flush();
        return RestfulEnvelope::successResponseTemplate('Tag added', $c,
            [new TagNormalizer()])->response();
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/tag/{tag}", options = { "expose" = true }, name="delete_tag")
     * @Method({"DELETE"})
     */
    public function deleteTagAction($tag)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TagRepository $tagRepository */
        $tagRepository = $em->getRepository(Tag::class);
        if( $result = $tagRepository->getTag($tag))
        {
            $em->remove($result);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Tag deleted', $result,
                [new TagNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Tag not found')->setStatus(410)->response();
    }

}
