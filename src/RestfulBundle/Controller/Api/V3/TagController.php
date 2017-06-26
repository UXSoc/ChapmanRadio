<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:37 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Tag;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\TagNormalizer;
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

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Put("/tag/{tag}",
     *     options = { "expose" = true },
     *     name="put_tag")
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
     * @Rest\Delete("/tag/{tag}",
     *     options = { "expose" = true },
     *     name="delete_tag")
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
