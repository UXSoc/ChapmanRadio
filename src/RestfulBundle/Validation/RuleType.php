<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/8/17
 * Time: 9:28 AM
 */

namespace RestfulBundle\Validation;


use Recurr\Rule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//https://webmozart.io/blog/2015/09/09/value-objects-in-symfony-forms/

class RuleType extends  AbstractType implements DataMapperInterface
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('freq',ChoiceType::class,[
            'choices'  => array(
                'YEARLY'   => 0,
                'MONTHLY'  => 1,
                'WEEKLY'   => 2,
                'DAILY'    => 3
            )
        ]);

        $builder->add('byDay',CollectionType::class,
        array(
           'entry_type' => ChoiceType::class,
            'entry_options' => array(
                'MO' => 'MO',
                'TU' => 'TU',
                'WE' => 'WE',
                'TH' => 'TH',
                'FR' => 'FR',
                'SA' => 'SA',
                'SU' => 'SU'
            ),
            'required' => false
        ));

        $builder->add('byMonth',CollectionType::class,
            array(
                'entry_type' => RangeType::class,
                'entry_options' => array(
                    'attr' => array(
                        'min' => 1,
                        'max' =>12
                    ),
                    'required' => false
                )
            ));
//
//        $builder->add('byYearDay',CollectionType::class,
//
//        )


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rule::class
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param mixed $data Structured data
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported.
     */
    public function mapDataToForms($data, $forms)
    {
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param mixed $data Structured data
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported.
     */
    public function mapFormsToData($forms, &$data)
    {
    }
}