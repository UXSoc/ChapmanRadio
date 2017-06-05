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
use CoreBundle\Helper\SuccessWrapper;
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
        $em = $this->getDoctrine()->getManager();
        /** @var TagRepository $tagRepository */
        $tagRepository = $em->getRepository(Tag::class);

        $limit = $request->get('limit',100);
        if($limit > 100)
            $limit = 100;
        $tags = $tagRepository->findTag($request->get('name',''),$limit);
        return $this->restful([new WrapperNormalizer(),
            new TagNormalizer()],new SuccessWrapper($tags,null));
    }
    /**
     * @Route("tag/{name}",
     *     options = { "expose" = true },
     *     name="get_tag")
     * @Method({"GET"})
     */
    public function getTag(Request $request,$name)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TagRepository $tagRepository */
        $tagRepository = $em->getRepository(Tag::class);

        $tag = $tagRepository->findOneBy(["tag" => $name]);
        if($tag == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Can't find tag"),400);
        return $this->restful([new WrapperNormalizer(),new TagNormalizer()],new SuccessWrapper($tag,"Found tag"));
    }

}
