<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/26/17
 * Time: 10:29 AM.
 */

namespace CoreBundle\Normalizer;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginatorNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
     * @param Paginator $object  object to normalize
     * @param string    $format  format the normalization result will be encoded as
     * @param array     $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $query = $object->getQuery();
        $count = $object->count();
        $perPage = $query->getMaxResults();
        $offset = $query->getFirstResult();

        return [
            'count'   => $count,
            'perPage' => $perPage,
            'pages'   => ceil($offset / $perPage),
            'result'  => array_map(function ($object) use ($format, $context) {
                return $this->normalizer->normalize($object, $format, $context);
            }, $query->getResult()),
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
        return $data instanceof Paginator;
    }
}
