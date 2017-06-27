<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/26/17
 * Time: 8:48 PM
 */

namespace RestfulBundle\Controller\Api\V3\Dashboard;


use CoreBundle\Entity\Tag;
use CoreBundle\Repository\TagRepository;
use FOS\RestBundle\Controller\FOSRestController;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/api/v3/")
 */
class TagController extends FOSRestController
{
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
            return new HttpException(409,"Category Already Exist");

        $c = new Tag();
        $c->setTag($tag);
        $em->persist($c);
        $em->flush();
        return $this->view(['tag' => $c]);
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
            return $this->view(['tag' => $result]);
        }
        return $this->createNotFoundException('Tag Not Found');
    }
}