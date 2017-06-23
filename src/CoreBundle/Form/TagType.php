<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/13/17
 * Time: 6:05 PM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Tag;
use CoreBundle\Form\DataTransformer\TagTransformer;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    private  $tagTransformer;

    function __construct(TagTransformer $tagTransformer)
    {
        $this->tagTransformer = $tagTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tag',TextType::class);
        $builder->get('tag')
            ->addModelTransformer($this->tagTransformer);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
            'csrf_protection' => false
        ]);
    }


}