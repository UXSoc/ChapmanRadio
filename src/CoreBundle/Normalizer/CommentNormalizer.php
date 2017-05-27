<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/26/17
 * Time: 2:04 AM
 */

namespace CoreBundle\Normalizer;


use CoreBundle\Entity\Blog;
use CoreBundle\Entity\Comment;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class CommentNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /** @var  NormalizerInterface */
    private  $normalizer;

    private $depth;

    function __construct($depth = 10)
    {
        $this->depth = $depth;
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
     * @param Comment $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = array())
    {

        return [
            'token' => $object->getToken(),
            'created_at' => $object->getCreateAt(),
            'content' => $object->getContent(),
            'user' => $this->normalizer->normalize($object->getUser(),$format,$context),
            'children' => array_map(function ($object) use ($format,$context){
                return $this->normalizer->normalize($object, $format, $context);
            },$object->getChildrenComments()->toArray())
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
        return $data instanceof Comment;
    }
}