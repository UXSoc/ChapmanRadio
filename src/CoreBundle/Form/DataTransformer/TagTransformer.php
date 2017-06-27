<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/22/17
 * Time: 8:30 PM
 */

namespace CoreBundle\Form\DataTransformer;


use CoreBundle\Entity\Tag;
use CoreBundle\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagTransformer implements DataTransformerInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param Tag $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }
        return $value->getTag();
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     * @param string $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $tag = $this->em->getRepository(Tag::class)->findOneBy(['tag' => $value]);

        if (!$tag) {
            $tag = new Tag();
            $tag->setTag($value);
        }

        return $tag;
    }
}