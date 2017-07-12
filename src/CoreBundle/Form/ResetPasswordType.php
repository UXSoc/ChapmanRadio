<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/13/17
 * Time: 3:27 PM
 */

namespace CoreBundle\Form;


use CoreBundle\Entity\Post;
use CoreBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Tests\Encoder\PasswordEncoder;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ResetPasswordType extends AbstractType
{
    /** @var  UserPasswordEncoder */
    private $passwordEncoder;

    /** @var  User */
    private  $user;

    function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->user = $options['user'];
        $builder->add('oldPassword',PasswordType::class,
            array('constraints' => array(new Callback(array($this, 'verifyOldPassword')))));
        $builder->add('newPassword',PasswordType::class,array());

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);

        $resolver->setRequired('user');
    }


    public function getBlockPrefix()
    {
        return 'reset_password';
    }

    public function verifyOldPassword($value, ExecutionContextInterface $context)
    {
        if(!$this->passwordEncoder->isPasswordValid($this->user,  $value))
        {
            $context->buildViolation('Invalid Password')
                ->addViolation();
        }
    }
}