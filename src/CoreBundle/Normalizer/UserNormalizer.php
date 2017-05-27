<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/26/17
 * Time: 9:12 AM
 */

namespace CoreBundle\Normalizer;


use CoreBundle\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class UserNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    function __construct()
    {

    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param User $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            "roles" => $object->getRoles(),
            "username" => $object->getUsername(),
            "created_at" => $object->getCreatedAt(),
            "updated_at" => $object->getUpdatedAt(),
            "token" => $object->getToken()
        ];
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
        return $data instanceof User;
    }
}