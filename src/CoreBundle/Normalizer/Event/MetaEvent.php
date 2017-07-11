<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/9/17
 * Time: 11:36 AM
 */

namespace CoreBundle\Normalizer\Event;

use CoreBundle\Entity\Post;
use CoreBundle\Entity\PostMeta;
use CoreBundle\Entity\Show;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

class MetaEvent implements EventSubscriberInterface
{

    private $router;
    private $dispatcher;
    private $em;

    function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * Returns the events to which this class has subscribed.
     *
     * Return format:
     *     array(
     *         array('event' => 'the-event-name', 'method' => 'onEventName', 'class' => 'some-class', 'format' => 'json'),
     *         array(...),
     *     )
     *
     * The class may be omitted if the class wants to subscribe to events of all classes.
     * Same goes for the format key.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize', 'class' => Post::class),
            array('event' => 'serializer.post_serialize', 'method' => 'onShowSerialize', 'class' => Show::class)
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var Post $entity */
        $entity = $event->getObject();
        if($meta = $entity->getMetaByKey(PostMeta::FEATURE))
        {
            $value = $meta->getValue();
            if(isset($value->mediaToken)) {
                $event->getVisitor()->addData('square', $this->router->generate('get_blog_media', [
                    'token' => $entity->getToken(),
                    'type' => 'square',
                    'media' => $value->mediaToken]));

                $event->getVisitor()->addData('wide', $this->router->generate('get_blog_media', [
                    'token' => $entity->getToken(),
                    'type' => 'wide',
                    'media' => $value->mediaToken]));
            }
        }
    }

    public function onShowSerialize(ObjectEvent $event)
    {

    }


}