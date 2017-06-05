<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:37 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Tag;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api/v3/")
 */
class TagController extends BaseController
{
    /**
     * @Route("tag",
     *     options = { "expose" = true },
     *     name="get_tags")
     * @Method({"GET"})
     */
    public function getTags(Request $request)
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->getDoctrine()->getManager()->getRepository(Tag::class);
        $pagination = $tagRepository->paginator($tagRepository->filter($request),
            $request->get('page',0),
            $request->get('entries',10),20);

        return RestfulEnvelope::successResponseTemplate(
            null,$pagination,[new TagNormalizer(),new PaginatorNormalizer()])->response();
    }
    /**
     * @Route("tag/{name}",
     *     options = { "expose" = true },
     *     name="get_tag")
     * @Method({"GET"})
     */
    public function getTag(Request $request,$name)
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->getDoctrine()->getManager()->getRepository(Tag::class);

        if($tag = $tagRepository->getTag($name))
            return RestfulEnvelope::successResponseTemplate("Found Tag",$tag,[new TagNormalizer()])->response();
        return RestfulEnvelope::errorResponseTemplate("Can't find tag")->setStatus(410)->response();
    }

}
