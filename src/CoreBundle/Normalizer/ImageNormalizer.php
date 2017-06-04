<?php
namespace CoreBundle\Normalizer;


use CoreBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

class ImageNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /** @var  NormalizerInterface */
    private  $normalizer;


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
     * @param Image $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $result = [
            'token' => $object->getToken(),
            'created_at' => $object->getCreatedAt(),
        ];

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
        return $data instanceof Image;
    }

}