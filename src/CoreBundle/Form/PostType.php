<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/13/17
 * Time: 12:20 PM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Post;
use CoreBundle\Form\Type\CategoryType;
use CoreBundle\Form\Type\TagType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class);
        $builder->add('slug',TextType::class);
        $builder->add('excerpt', TextType::class);
        $builder->add('isPinned', CheckboxType::class);
        $builder->add('content',TextareaType::class);
        $builder->add('categories', CollectionType::class, array(
            'entry_type'   => CategoryType::class,
            'allow_add' => true,
            'allow_delete' => true
        ));
        $builder->add('tags', CollectionType::class, array(
            'entry_type'   => TagType::class,
            'allow_add' => true,
            'allow_delete' => true
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'csrf_protection' => false
        ]);
    }

}