<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 6:48 PM
 */

namespace RestfulBundle\Controller\Uploads;

use CoreBundle\Entity\Media;
use CoreBundle\Event\MediaRetrieveEvent;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/uploads/")
 */
class MediaController extends FOSRestController
{
    /**
     * @Rest\Get("media/{source}.{ext}",
     *     name="get_media")
     */
    public function getImageAction(Request $request,$source,$ext)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var  $mediaController */
        $mediaController = $em->getRepository(Media::class);
        /** @var Media $media*/
        $media = $mediaController->findOneBy(['source' => $source]);

       $mimeTypeGuess = new MimeTypeExtensionGuesser();
        if($media) {
            if(!$mimeTypeGuess->guess($media->getMime()) === $ext)
                throw  $this->createNotFoundException('media not found');

            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');

            $event = new MediaRetrieveEvent($media);
            $dispatcher->dispatch(MediaRetrieveEvent::NAME,$event);
            return $this->file($event->getPath());
        }
        throw $this->createNotFoundException('media not found');
    }
}