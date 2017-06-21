<?php

namespace CoreBundle\Normalizer;

use CoreBundle\Entity\Post;
use CoreBundle\Entity\Tag;
use DBlackborough\Quill\Render;
use Psr\Cache\CacheItemPoolInterface;
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
        $result = [
            'token' => $object->getToken(),
            'slug' => $object->getSlug(),
            'name' => $object->getName(),
            'created_at' => $object->getCreatedAt(),
            'updated_at' => $object->getUpdatedAt(),
            'excerpt' => $object->getExcerpt(),
            'categories' => $object->getCategories() != null ? $object->getCategories()->getKeys() : null,
            'tags' => $object->getTags() != null ? $object->getTags()->getKeys() : null,
            'is_pinned' => (boolean)$object->isPinned(),
            'author' => $this->normalizer->normalize($object->getAuthor(), $format, $context)
        ];
        if(array_key_exists('delta',$context) && $context['delta'] === true) {
            $result['content'] = $object->getContent();
        } else {
            $quill = new Render($object->getContent(),'HTML');
            $result['content'] = $quill->render();
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