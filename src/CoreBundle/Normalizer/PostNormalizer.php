<?php

namespace CoreBundle\Normalizer;

use CoreBundle\Entity\Post;
use CoreBundle\Entity\Tag;
use DBlackborough\Quill\Render;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/26/17
 * Time: 1:46 AM
 */
class PostNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /** @var  NormalizerInterface */
    private $normalizer;

    private $cacheService;

    function __construct(CacheItemPoolInterface  $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Sets the owning Normalizer object.
     *
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param Post $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|\Symfony\Component\Serializer\Normalizer\scalar
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $bag = new ParameterBag($context);

        $result = [
            'token' => $object->getToken(),
            'slug' => $object->getSlug(),
            'name' => $object->getName(),
            'created_at' => $this->normalizer->normalize($object->getCreatedAt(),$format,$context),
            'updated_at' => $this->normalizer->normalize($object->getUpdatedAt(),$format,$context),
            'excerpt' => $object->getExcerpt(),
            'categories' => $object->getCategories() != null ? $object->getCategories()->getKeys() : null,
            'tags' => $object->getTags() != null ? $object->getTags()->getKeys() : null,
            'is_pinned' => (boolean)$object->isPinned(),
            'author' => $this->normalizer->normalize($object->getAuthor(), $format, $context)
        ];
        if ($bag->get('delta', false)) {
            $quill = new Render($object->getContent(), 'HTML');
            $result['content'] = $quill->render();
        } else {
            $result['content'] = $object->getContent();
        }

        return $result;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Post;
    }

}