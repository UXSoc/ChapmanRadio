<?php namespace CoreBundle\Form;


use CoreBundle\Entity\Post;
use CoreBundle\Entity\Tag;
use CoreBundle\Form\DataTransformer\TagTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    private $tagTransformer;
    function __construct(TagTransformer $tagTransformer)
    {
        $this->tagTransformer = $tagTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content',TextareaType::class,array());
        $builder->add('name',TextType::class,array());
        $builder->add('excerpt',TextareaType::class,array());
        $builder->add('slug',TextType::class,array());
        $builder->add('isPinned',CheckboxType::class,array());
        $builder->add('tags',CollectionType::class,array(
            'entry_type'   => TagType::class,
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'csrf_protection' => false
        ]);
    }
}