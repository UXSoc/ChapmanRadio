<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/26/17
 * Time: 12:21 PM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Dj;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DjType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dj::class,
            'csrf_protection' => false
        ]);
    }

}