<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/8/17
 * Time: 10:11 AM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Comment;
use CoreBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email',TextareaType::class,array());
        $builder->add('name',TextareaType::class,array());
        $builder->add('username',TextareaType::class,array());
        $builder->add('plainTextPassword',TextareaType::class,array());
        $builder->add('studentId',TextareaType::class,array());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false
        ]);
    }

}