<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/10/17
 * Time: 11:52 PM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName',TextType::class,array());
        $builder->add('lastName',TextType::class,array());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
            'csrf_protection' => false
        ]);
    }
}