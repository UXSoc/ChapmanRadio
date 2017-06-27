<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/26/17
 * Time: 12:13 PM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Show;
use CoreBundle\Form\Type\GenreType;
use CoreBundle\Form\Type\TagType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class);
        $builder->add('slug',TextType::class);
        $builder->add('description', TextType::class);
        $builder->add('profanity',CheckboxType::class);
        $builder->add('enableComments',CheckboxType::class);
        $builder->add('excerpt',TextareaType::class);
        $builder->add('genres', CollectionType::class, array(
            'entry_type'   => GenreType::class,
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
            'data_class' => Show::class,
            'csrf_protection' => false
        ]);
    }
}