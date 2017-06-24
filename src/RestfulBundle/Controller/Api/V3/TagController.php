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
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Repository\TagRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api/v3/")
 */
class TagController extends FOSRestController
{
    /**
     * @Route("tag",
     *     options = { "expose" = true },
     *     name="get_tags")
     * @Method({"GET"})
     */
    public function getTagsAction(Request $request)
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->getDoctrine()->getManager()->getRepository(Tag::class);

        $page = (int)$request->get('page',0);
        $perPage = (int)$request->get('perPage',20);

        $tags = $tagRepository->paginator($tagRepository->filter($request), $page, $perPage,20)->getQuery()->getResult();

        return $this->view([
            "page" => $page,
            "perPage" => $perPage,
            "tags" => array_map(function($value) {
                return $value->getTag();
            },$tags)
        ]);
    }
    /**
     * @Route("tag/{name}",
     *     options = { "expose" = true },
     *     name="get_tag")
     * @Method({"GET"})
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
     * @Route("/tag/{tag}",
     *     options = { "expose" = true },
     *     name="put_tag")
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
     * @Route("/tag/{tag}",
     *     options = { "expose" = true },
     *     name="delete_tag")
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
