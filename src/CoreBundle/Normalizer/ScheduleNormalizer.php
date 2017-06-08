<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/7/17
 * Time: 9:25 PM
 */

namespace CoreBundle\Normalizer;


use Carbon\Carbon;
use CoreBundle\Entity\Schedule;
use Symfony\Bundle\SecurityBundle\Tests\Functional\Bundle\AclBundle\Entity\Car;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ScheduleNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /** @var  NormalizerInterface */
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
     * @param Schedule $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'start_date' => $this->normalizer->supportsNormalization($object->getStartDate(),$format) ? $this->normalizer->normalize($object->getStartDate(),$format,["datetime-format" => "YYYY-MM-DD"]): $object->getStartDate(),
            'end_date' =>  $this->normalizer->supportsNormalization($object->getEndDate(),$format) ? $this->normalizer->normalize($object->getEndDate(),$format,["datetime-format" => "YYYY-MM-DD"]): $object->getEndDate(),
            'start_time' => $this->normalizer->supportsNormalization($object->getStartTime(),$format) ? $this->normalizer->normalize($object->getStartTime(),$format,["datetime-format" => "HH:MM:SS"]): $object->getStartTime(),
            'end_time' => $this->normalizer->supportsNormalization($object->getEndTime(),$format) ? $this->normalizer->normalize($object->getEndTime(),$format,["datetime-format" => "HH:MM:SS"]): $object->getEndTime(),
            'show' => $this->normalizer->supportsNormalization($object->getShow(),$format)? $this->normalizer->normalize($object->getShow(),$format,$context): null,
            'meta' => $object->getMeta()
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
        return $data instanceof Schedule;
    }
}