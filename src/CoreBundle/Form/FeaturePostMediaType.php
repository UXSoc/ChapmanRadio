<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 11:35 PM
 */

namespace CoreBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;

class FeaturePostMediaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder->add('feature', TextType::class, array());

        $builder->add('xWide', NumberType::class, array());
        $builder->add('yWide', NumberType::class, array());
        $builder->add('widthWide', NumberType::class, array());
        $builder->add('heightWide', NumberType::class, array());

        $builder->add('xSquare', NumberType::class, array());
        $builder->add('ySquare', NumberType::class, array());
        $builder->add('widthSquare', NumberType::class, array());
        $builder->add('heightSquare', NumberType::class, array());

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