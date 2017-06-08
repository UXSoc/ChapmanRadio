<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 9:26 AM.
 */

namespace CoreBundle\Normalizer;

use CoreBundle\Entity\Genre;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GenreNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
     * @param Genre  $object  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->getGenre();
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
        return $data instanceof Genre;
    }
}
