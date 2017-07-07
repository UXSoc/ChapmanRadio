<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/27/17
 * Time: 12:47 PM
 */

namespace CoreBundle\Form\Type;


use CoreBundle\Entity\Image;
use CoreBundle\Form\DataTransformer\ImageTypeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{

    private  $transfomer;

    function __construct(ImageTypeTransformer $transformer)
    {
        $this->transfomer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transfomer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'csrf_protection' => false,
            'invalid_message' => 'Unknown Image Comment'
        ]);
    }

    public function getParent()
    {
        return FileType::class;
    }

}