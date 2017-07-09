<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 9:20 PM
 */

namespace RestfulBundle\Controller\Api\V3\Dashboard;

use CoreBundle\Entity\Media;
use CoreBundle\Event\MediaRetrieveEvent;
use CoreBundle\Event\MediaSaveEvent;
use CoreBundle\Form\MediaType;
use CoreBundle\Repository\MediaRepository;
use CoreBundle\Security\MediaVoter;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/api/v3/")
 */
class MediaController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Post("media",
     *     options = { "expose" = true },
     *     name="post_media")
     */
    public function postMediaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $media = new Media();
        $media->setAuthor($this->getUser());
        $form = $this->createForm(MediaType::class,$media);
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');

            $event = new MediaSaveEvent($media);
            $dispatcher->dispatch(MediaSaveEvent::NAME,$event);
            $em->persist($media);
            $em->flush();
            return $this->view(['media' => $media]);
        }
        return $this->view($form);
    }

    /**
     * @Rest\Patch("media/{token}",
     *     options = { "expose" = true },
     *     name="patch_media")
     */
    public function patchMediaAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var MediaRepository $mediaRepository */
        $mediaRepository = $em->getRepository(Media::class);

        if($media = $mediaRepository->getMediaByToken($token))
        {
            $this->denyAccessUnlessGranted(MediaVoter::EDIT, $media);

            $form = $this->createForm(MediaType::class,$media,['method' => 'patch']);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($media);
                $em->flush();
                return $this->view(['media' => $media]);
            }
            return $this->view($form);
        }
        throw $this->createNotFoundException('Media Not Found');
    }


    /**
     * @Rest\Get("media/{token}",
     *     options = { "expose" = true },
     *     name="get_media")
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
     */
    public function getMediasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var MediaRepository $mediaRepository */
        $mediaRepository = $em->getRepository(Media::class);

        return ['payload' => $mediaRepository->paginator($mediaRepository->filter($request),
            $request->get('page',0),
            $request->get('perPage',40),40)];

    }



}