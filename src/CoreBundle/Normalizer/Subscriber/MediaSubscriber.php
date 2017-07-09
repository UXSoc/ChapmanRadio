<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 9:45 PM
 */

namespace CoreBundle\Normalizer\Subscriber;


use CoreBundle\Entity\Media;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\Routing\RouterInterface;

class MediaSubscriber implements SubscribingHandlerInterface
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
                'type' => Media::class,
                'method' => 'serializeDateTimeToJson',
            ),
        );
    }

    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, Media $date, array $type, Context $context)
    {

        $mimeTypeGuess = new MimeTypeExtensionGuesser();
        $context->setGroups(['detail']);
        return [
            'created_at' => $context->accept($date->getCreatedAt()),
            'updated_at' => $context->accept($date->getUpdatedAt()),
            'token' => $date->getToken(),
            'title' => $date->getTitle(),
            'caption' => $date->getCaption(),
            'alt_text' => $date->getAltText(),
            'description' => $date->getDescription(),
            'author' => $context->accept($date->getAuthor()),
            'mime' => $date->getMime(),
            'is_hidden' => $date->getisHidden(),
            'path' =>  $this->router->generate('get_media',['source' => $date->getSource(), 'ext' => $mimeTypeGuess->guess($date->getMime())])
        ];
    }

}