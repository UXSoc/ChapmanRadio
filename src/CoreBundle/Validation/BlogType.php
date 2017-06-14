<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/13/17
 * Time: 12:20 PM
 */

namespace CoreBundle\Validation;


use CoreBundle\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'csrf_protection' => false
        ]);
    }

}