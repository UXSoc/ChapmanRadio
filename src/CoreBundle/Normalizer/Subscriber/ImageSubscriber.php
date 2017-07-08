<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 11:00 PM
 */

namespace CoreBundle\Normalizer\Subscriber;


use CoreBundle\Entity\Image;
use CoreBundle\Event\ImageRetrieveEvent;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class ImageSubscriber implements SubscribingHandlerInterface
{
    private $dispatcher;
    private  $router;

    function __construct(EventDispatcherInterface $dispatcher, RouterInterface $router)
    {
        $this->dispatcher = $dispatcher;
        $this->router = $router;
    }

    /**
     * Return format:
     *
     *      array(
     *          array(
     *              'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
     *              'format' => 'json',
     *              'type' => 'DateTime',
     *              'method' => 'serializeDateTimeToJson',
     *          ),
     *      )
     *
     * The direction and method keys can be omitted.
     *
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => Image::class,
                'method' => 'serializeDateTimeToJson',
            ),
        );
    }

    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, Image $date, array $type, Context $context)
    {
        $event = new ImageRetrieveEvent($date);
        $this->dispatcher->dispatch(ImageRetrieveEvent::NAME,$event);
        return [
            'created_at' => $date->getCreatedAt(),
            'path' =>  $event->getPath()
        ];
    }
}