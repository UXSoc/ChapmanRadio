<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/7/17
 * Time: 1:25 PM
 */

namespace CoreBundle\Normalizer;


use Carbon\Carbon;
use CoreBundle\Helper\ScheduleEntry;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ScheduleEntryNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
     * @param ScheduleEntry $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'show' => $this->normalizer->normalize($object->getShow(),$format,$context),
            'date' => $this->normalizer->normalize($object->getShowDate(),$format,$context),
            'length' => $object->getLenght()
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
        return $data instanceof ScheduleEntry;
    }
}