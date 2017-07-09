<?php
namespace RestfulBundle\Controller\Uploads;

use CoreBundle\Entity\Image;
use CoreBundle\Event\ImageRetrieveEvent;
use CoreBundle\Repository\ImageRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/uploads/")
 */
class ImageController extends FOSRestController
{
    /**
     * @Rest\Get("images/{source}.png",
     *     name="get_image")
     */
    public function getImageAction(Request $request,$source)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ImageRepository $imageRepository */
        $imageRepository = $em->getRepository(Image::class);
        /** @var Image $image */
        $image = $imageRepository->findOneBy(['source' => $source]);

        if($image) {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');

            $event = new ImageRetrieveEvent($image);
            $dispatcher->dispatch(ImageRetrieveEvent::NAME,$event);
            return $this->file($event->getPath());
        }
        throw $this->createNotFoundException('image not found');
    }
}