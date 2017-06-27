<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/24/17
 * Time: 6:12 PM
 */

namespace CoreBundle\Normalizer\Subscriber;


use CoreBundle\Entity\Category;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;



class CategorySubscriber implements SubscribingHandlerInterface
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
                'type' => Category::class,
                'method' => 'serializeDateTimeToJson',
            ),
        );
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param Category $date
     * @param array $type
     * @param Context $context
     * @return string
     */
    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, Category $date, array $type, Context $context)
    {
        return $date->getCategory();
    }
}