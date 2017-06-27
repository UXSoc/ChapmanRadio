<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/25/17
 * Time: 10:34 PM
 */

namespace CoreBundle\Normalizer\Event;


use CoreBundle\Entity\Post;
use CoreBundle\Entity\Show;
use DBlackborough\Quill\Render;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class DeltaEvent implements EventSubscriberInterface
{
    private $postProcessed;
    private $showProcessed;
    function __construct()
    {
        $this->postProcessed = [];
        $this->showProcessed = [];
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
            array('event' => 'serializer.pre_serialize', 'method' => 'onPrePostSerialize', 'class' => Post::class),
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreShowSerialize', 'class' => Show::class)
        );
    }

    public function onPrePostSerialize(PreSerializeEvent $event)
    {
        /** @var Post $entity */
        $entity = $event->getObject();
        if(!array_key_exists($entity->getId(),$this->postProcessed)) {
            $entity->setContent($this->render($entity->getDeltaRenderer(), $entity->getContent()));
        }
        $this->postProcessed[$entity->getId()] = null;
    }

    public function onPreShowSerialize(PreSerializeEvent $event)
    {
        /** @var Show $entity */
        $entity = $event->getObject();
        if(!array_key_exists($entity->getId(),$this->showProcessed)) {
            $entity->setDescription($this->render($entity->getDeltaRenderer(), $entity->getDescription()));
        }
        $this->showProcessed[$entity->getId()] = null;
    }


    private function render($type,$content)
    {
        switch ($type)
        {
            case 'HTML':
                $quill = new Render($content, 'HTML');
                return $quill->render();
                break;
            default:
                return $content;
                break;
        }
    }
}