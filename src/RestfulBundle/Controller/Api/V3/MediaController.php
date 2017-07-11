<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/10/17
 * Time: 8:26 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Media;
use CoreBundle\Repository\MediaRepository;
use FOS\RestBundle\Controller\FOSRestController;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/")
 */
class MediaController extends FOSRestController
{
    /**
     * @Rest\Get("media/{token}",
     *     options = { "expose" = true },
     *     name="get_media")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getMediaAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var MediaRepository $mediaRepository */
        $mediaRepository = $em->getRepository(Media::class);

        if($media = $mediaRepository->getMediaByToken($token))
        {
            return $this->view(['media' => $media],200);
        }
        throw $this->createNotFoundException('Media Not Found');
    }


    /**
     * @Rest\Get("media",
     *     options = { "expose" = true },
     *     name="get_medias")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getMediasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var MediaRepository $mediaRepository */
        $mediaRepository = $em->getRepository(Media::class);

        return $this->view(['payload' => $mediaRepository->paginator($mediaRepository->filter($request),
            $request->get('page',0),
            $request->get('perPage',40),40)]);

    }
}