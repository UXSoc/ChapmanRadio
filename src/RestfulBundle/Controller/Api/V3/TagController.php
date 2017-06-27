<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:37 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Tag;
use CoreBundle\Repository\TagRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v3/")
 */
class TagController extends FOSRestController
{
    /**
     * @Rest\Get("tag",
     *     options = { "expose" = true },
     *     name="get_tags")
     */
    public function getTagsAction(Request $request)
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->getDoctrine()->getManager()->getRepository(Tag::class);

        return $this->view(['payload' =>
            $tagRepository->paginator($tagRepository->filter($request), (int)$request->get('page',0), (int)$request->get('perPage',20),20)]);


    }
    /**
     * @Rest\Get("tag/{name}",
     *     options = { "expose" = true },
     *     name="get_tag")
     */
    public function getTagAction(Request $request,$name)
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->getDoctrine()->getManager()->getRepository(Tag::class);

        /** @var Tag $tag */
        if($tag = $tagRepository->getTag($name))
            return $this->view($tag->getTag());
        throw $this->createNotFoundException("Tag Not Found");
    }

}
