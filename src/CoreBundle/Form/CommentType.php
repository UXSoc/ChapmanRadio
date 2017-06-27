<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/26/17
 * Time: 10:37 AM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Comment;
use CoreBundle\Form\DataTransformer\CommentTransformer;
use CoreBundle\Form\Type\ParentCommentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    private  $commentTransformer;

    function __construct(CommentTransformer $commentTransformer)
    {
        $this->commentTransformer = $commentTransformer;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content',TextType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA,function (FormEvent $event){
            /** @var Comment $object */
            $object = $event->getData();
            $form = $event->getForm();
            if($object->getParentComment() === null)
            {
                $form->add('parentComment',ParentCommentType::class,array(
                    'required' => false
                ));
            }

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => true,
            'data_class' => Comment::class,
            'csrf_protection' => false
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}