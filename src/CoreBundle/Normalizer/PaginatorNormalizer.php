<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/26/17
 * Time: 10:29 AM
 */

namespace CoreBundle\Normalizer;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class PaginatorNormalizer implements SubscribingHandlerInterface
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
                'type' => Paginator::class,
                'method' => 'serializeDateTimeToJson',
            ),
        );
    }

    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, Paginator $date, array $type, Context $context)
    {

        $query = $date->getQuery();
        $count = $date->count();
        $perPage = $query->getMaxResults();
        $offset = $query->getFirstResult();

        return [
            "count" => $count,
            "perPage" => $perPage,
            "pages" => ceil( $offset/$perPage),
            "result" => array_map(function ($object) use ($context)
            {
               return $context->accept($object);
            },$query->getResult())
        ];
    }
}