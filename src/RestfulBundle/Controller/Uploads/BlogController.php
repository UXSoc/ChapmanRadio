<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/9/17
 * Time: 3:15 PM
 */

namespace RestfulBundle\Controller\Uploads;

use CoreBundle\Entity\Media;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\PostMeta;
use CoreBundle\Event\MediaRetrieveEvent;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Repository\MediaRepository;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Service\ImageCache;
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
class BlogController extends FOSRestController
{


    /**
     * @Rest\Get("post/{token}/feature/{type}/{media}.png",
     *     name="get_blog_media")
     */
    public function getPostFeatureSquareAction(Request $request, $token,$type, $media)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ImageCache $imageCache */
        $imageCache = $this->get(ImageCache::class);

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if ($post = $postRepository->getPostByToken($token)) {
            $feature = $post->getMetaByKey(PostMeta::FEATURE)->getValue();
            if(isset($feature->square) && isset($feature->mediaToken) && $feature->mediaToken === $media)
            {
                /** @var MediaRepository $mediaRepository */
                $mediaRepository = $em->getRepository(Media::class);
                /** @var Media $media */
                if($media = $mediaRepository->getMediaByToken($feature->mediaToken))
                {
                    /** @var EventDispatcher $dispatcher */
                    $dispatcher = $this->get('event_dispatcher');
                    $event = new MediaRetrieveEvent($media);
                    $dispatcher->dispatch(MediaRetrieveEvent::NAME,$event);
                    switch ($type){
                        case 'wide':
                            return $this->file($imageCache->resolve($event->getPath(),$feature->wide));
                        case 'square':
                            return $this->file($imageCache->resolve($event->getPath(),$feature->square));
                    }
                }
            }
        }
        throw  $this->createNotFoundException("Image not Found");
    }

}