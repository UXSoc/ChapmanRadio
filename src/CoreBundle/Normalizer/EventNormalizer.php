<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/31/17
 * Time: 10:40 PM.
 */

namespace CoreBundle\Normalizer;

use CoreBundle\Entity\Event;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EventNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
     * @param Event  $object  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
          'start' => $object->getStart(),
          'end'   => $object->getEnd(),
          'show'  => $this->normalizer->normalize($object->getShow(), $format, $context),
        ];
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
        return $data instanceof Event;
    }
}
