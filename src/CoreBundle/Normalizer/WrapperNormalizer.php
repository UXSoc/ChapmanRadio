<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/27/17
 * Time: 7:10 PM
 */

namespace CoreBundle\Normalizer;


use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class WrapperNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
     * @param ErrorWrapper|SuccessWrapper $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if($object instanceof SuccessWrapper)
        {
           return [
               'success' => true,
               'message' => $object->getMessage(),
               'data' => $this->normalizer->normalize($object->getPayload(),$format,$context),
           ];
        }
        else
        {
            return [
                'success' => false,
                'message' => $object->getMessage(),
                'errors' => $object->getErrors()
            ];
        }
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
        return ($data instanceof ErrorWrapper) | ($data instanceof SuccessWrapper);
    }



}