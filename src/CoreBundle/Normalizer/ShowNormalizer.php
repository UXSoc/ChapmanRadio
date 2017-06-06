<?php
namespace CoreBundle\Normalizer;

use CoreBundle\Entity\Dj;
use CoreBundle\Entity\Show;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShowNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
     * @param Show $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'token' => $object->getToken(),
            'slug' => $object->getSlug(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'created_at' => $object->getCreatedAt(),
            'profanity' => $object->getProfanity(),
            'updated_at' => $object->updatedAt(),
            'enable_comments' => $object->getEnableComments(),
            'header_image' => $object->getHeaderImage(),
            'excerpt' => $object->getExcerpt(),
            'tags' => $object->getTags()->getKeys(),
            'genres' => $object->getGenres()->getKeys(),
            'djs' => array_map(function (Dj $dj) use ($format,$context) {
                if($this->normalizer->supportsNormalization($dj,$format))
                {
                    return $this->normalizer->normalize($dj,$format,$context);
                }
                return [];
            }, $object->getDjs()->toArray())
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
        return $data instanceof Show;
    }
}