<?php

namespace CoreBundle\Normalizer;

use CoreBundle\Entity\Post;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/26/17
 * Time: 1:46 AM.
 */
class PostNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /** @var NormalizerInterface */
    private $normalizer;

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
     * @param Post   $object  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|\Symfony\Component\Serializer\Normalizer\scalar
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $result = [
            'token'      => $object->getToken(),
            'slug'       => $object->getSlug(),
            'name'       => $object->getName(),
            'created_at' => $object->getCreatedAt(),
            'updated_at' => $object->getUpdatedAt(),
            'excerpt'    => $object->getExcerpt(),
            'categories' => $object->getCategories() != null ? $object->getCategories()->getKeys() : null,
            'tags'       => $object->getTags() != null ? $object->getTags()->getKeys() : null,
            'is_pinned'  => (bool) $object->isPinned(),
            'author'     => $this->normalizer->normalize($object->getAuthor(), $format, $context),
            'content'    => $object->getContent(),
        ];

        return $result;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Post;
    }
}
