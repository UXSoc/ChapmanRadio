<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/24/17
 * Time: 6:04 PM
 */

namespace CoreBundle\Normalizer\Subscriber;

use CoreBundle\Entity\Tag;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;


class TagSubscriber implements SubscribingHandlerInterface
{

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
                'type' => Tag::class,
                'method' => 'serializeDateTimeToJson',
            ),
        );
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param Tag $date
     * @param array $type
     * @param Context $context
     * @return string
     */
    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, Tag $date, array $type, Context $context)
    {
        return $date->getTag();
    }
}