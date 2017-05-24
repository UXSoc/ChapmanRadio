<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller;

use AppBundle\Form\UserRegisterType;
use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\User;
use CoreBundle\Helper\RestfulHelper;
use CoreBundle\Repository\UserRepository;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends BaseController
{
    /**
     * @Route("/register", options = { "expose" = true }, name="register")
     */
    public function RegisterAction(Request $request)
    {

        $bag = $this->getJsonPayloadAsParameterBag();
        $user = new User();
        $user->setName($bag->get("name"));
        $user->setUsername($bag->get("username"));
        $user->setEmail($bag->get("email"));
        $user->setPlainPassword($bag->get("password"));
        $user->setStudentId($bag->get("studentId"));

        $errors = $this->validateEntity($user);
        if(count($errors) == 0)
        {
            $user->setConfirmationToken(substr(md5(random_bytes(10)),20));

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);


            $message =new Swift_Message();
            $message->setSubject('Welcome')
                ->setFrom($user->getEmail())
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'auth/email/confirm.html.twig',
                        array('user' => $user)
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return RestfulHelper::success("User Registered");
        }
        return RestfulHelper::error(400,"Couldn't Register User",$errors);
    }

    /**
     * @Route("/confirm/{token}", options = { "expose" = true }, name="confirm_token")
     */
    public function confirmationAction(Request $request,$token)
    {

        /** @var $user User*/
        $user =  $this->getDoctrine()->getRepository('core.user_repository')->findOneBy(array('confirmationToken' => $token));
        if (!$user) {
            return RestfulHelper::error(400,"Unknown Confirmation Token",[]);
        }
        $user->setConfirmationToken(null);
        $user->setConfirmed(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return RestfulHelper::success("Confirmation Token is Valid");

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/user/status", options = { "expose" = true }, name="user_status")
     */
    public function postLoggedInUser(Request $request)
    {}



}