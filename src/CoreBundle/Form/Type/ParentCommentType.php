<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/26/17
 * Time: 11:26 AM
 */

namespace CoreBundle\Form\Type;


use CoreBundle\Entity\Comment;
use CoreBundle\Form\DataTransformer\CommentTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParentCommentType  extends AbstractType
{
    private  $transfomer;

    function __construct(CommentTransformer $transformer)
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
            'data_class' => Comment::class,
            'csrf_protection' => false,
            'invalid_message' => 'Unknown Parent Comment'
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}