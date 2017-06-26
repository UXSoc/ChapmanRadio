<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/13/17
 * Time: 6:05 PM
 */

namespace CoreBundle\Form\Type;


use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Tag;
use CoreBundle\Form\DataTransformer\TagTransformer;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    private  $transformer;

    function __construct(TagTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
            'csrf_protection' => false
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}