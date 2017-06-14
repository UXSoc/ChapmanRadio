<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/13/17
 * Time: 3:27 PM
 */

namespace CoreBundle\Validation;


use CoreBundle\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content',TextareaType::class,array());
        $builder->add('name',TextType::class,array());
        $builder->add('excerpt',TextareaType::class,array());
        $builder->add('name',TextType::class,array());
        $builder->add('slug',TextType::class,array());
        $builder->add('isPinned',CheckboxType::class,array());

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'csrf_protection' => false
        ]);
    }
}