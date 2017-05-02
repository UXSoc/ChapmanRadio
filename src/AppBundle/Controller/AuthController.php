<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/25/17
 * Time: 10:03 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Users;
use AppBundle\Form\UserConfirmType;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use ChapmanRadio\DB;
use ChapmanRadio\Imaging;
use ChapmanRadio\Notify;
use ChapmanRadio\Request as ChapmanRadioRequest;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use ChapmanRadio\Uploader;
use ChapmanRadio\UserModel;
use ChapmanRadio\Util;
use ChapmanRadio\Validation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\User;


class AuthController extends Controller
{
    /**
     * @Route("/join", name="join")
     */
    public function RegisterAction(Request $request)
    {

        /** @var UserRepository $userRepository */
        $userRepository = $this->get('user_repository');
        $user = $userRepository->create();

        /** @var $form Form*/
        $form = $this->createForm(UserType::class,$user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setConfirmationToken(substr(md5(random_bytes(10)),20));

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $message = \Swift_Message::newInstance()
                ->setSubject('Welcome')
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

            return $this->render('auth/register_confirm.html.twig');
        }

        return $this->render('auth/register.html.twig', array("join_form" => $form->createView()));
    }



    /**
     * @Route("/confirm/{token}", name="confirm_token")
     */
    public function confirmationAction(Request $request,$token)
    {

        /** @var $user Users*/
        $user =  $this->getDoctrine()->getRepository('AppBundle:Users')->findOneBy(array('confirmation_token' => $token));
        if (!$user) {
            throw $this->createNotFoundException('Unknown confimration key');
        }
        $user->setConfirmationToken(null);
        $user->setConfirmed(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('login'));

    }

    /**
     * @Route("/login", name="login")
     */
    public  function  loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
}