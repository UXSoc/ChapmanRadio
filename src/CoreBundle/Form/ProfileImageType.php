<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/27/17
 * Time: 12:24 PM
 */

namespace CoreBundle\Form;


use CoreBundle\Form\Type\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileImageType  extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder->add('image', ImageType::class, array());
        $builder->add('x', NumberType::class, array());
        $builder->add('y', NumberType::class, array());
        $builder->add('width', NumberType::class, array());
        $builder->add('height', NumberType::class, array());
    }

    public function getBlockPrefix()
    {
        return 'profile_image';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }

}