<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/13/17
 * Time: 6:05 PM
 */

namespace CoreBundle\Validation;


use CoreBundle\Entity\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('meta',RuleType::class,array());
        $builder->add('startDate',DateTimeType::class,array());
        $builder->add('endDate',DateTimeType::class,array());
        $builder->add('startTime',TimeType::class);
        $builder->add('endTime',TimeType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
            'csrf_protection' => false
        ]);
    }


}