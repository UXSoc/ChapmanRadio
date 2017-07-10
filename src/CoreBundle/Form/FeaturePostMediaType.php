<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 11:35 PM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Media;
use CoreBundle\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hamcrest\Thingy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContext;

class FeaturePostMediaType extends AbstractType
{

    private $em;

    function __construct( EntityManagerInterface $em)
    {
        $this->em =  $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder->add('mediaToken', TextType::class, array());

        $builder->add('xWide', NumberType::class, array());
        $builder->add('yWide', NumberType::class, array());
        $builder->add('widthWide', NumberType::class, array());
        $builder->add('heightWide', NumberType::class, array());

        $builder->add('xSquare', NumberType::class, array());
        $builder->add('ySquare', NumberType::class, array());
        $builder->add('widthSquare', NumberType::class, array());
        $builder->add('heightSquare', NumberType::class, array());

    }

    public function getBlockPrefix()
    {
        return 'profile_image';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'constraints' => [
                new Callback([
                    'callback' => [$this,'verifyMediaToken']
                ])
            ]

        ]);
    }

    public function verifyMediaToken($data, ExecutionContext $context)
    {
        /** @var MediaRepository $mediaRepository */
        $mediaRepository  = $this->em->getRepository(Media::class);
        if(!$mediaRepository->getMediaByToken($data['mediaToken']))
        {
            $context->buildViolation('Invalid Media Token')
                ->atPath('mediaToken')
                ->addViolation();
        }
    }

}