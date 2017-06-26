<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/26/17
 * Time: 10:28 AM
 */

namespace CoreBundle\Form\Type;


use CoreBundle\Entity\Genre;
use CoreBundle\Form\DataTransformer\GenreTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenreType  extends AbstractType
{
    private  $genreTransformer;

    function __construct(GenreTransformer $genreTransformer)
    {
        $this->genreTransformer = $genreTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->genreTransformer);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Genre::class,
            'csrf_protection' => false
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}