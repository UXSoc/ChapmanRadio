<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller\Api\V3;

use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\User;
use CoreBundle\Helper\RestfulHelper;
use CoreBundle\Helper\RestfulJsonResponse;
use CoreBundle\Repository\UserRepository;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/api/v3")
 */
class AuthController extends BaseController
{
    /**
     * @Route("/auth/register", options = { "expose" = true }, name="post_register")
     * @Method({"POST"})
     */
    public function RegisterAction(Request $request)
    {
        $restfulJson = new RestfulJsonResponse();

        $bag = $this->getJsonPayloadAsParameterBag();
        $user = new User();
        $user->setName($bag->get("name"));
        $user->setUsername($bag->get("username"));
        $user->setEmail($bag->get("email"));
        $user->setPlainPassword($bag->get("password"));
        $user->setStudentId($bag->get("studentId"));

        $restfulJson->addErrors($this->validateEntity($user));
        if (!$restfulJson->hasErrors()) {
            $user->setConfirmationToken(substr(md5(random_bytes(10)), 20));

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);


            $message = new Swift_Message();
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

            $restfulJson->setMessage("User Registered");
            return $restfulJson;
        }
        $restfulJson->setStatusCode(400);
        $restfulJson->setMessage("Couldn't Register User");
        return $restfulJson;
    }

    /**
     * @Route("/auth/confirm/{token}", options = { "expose" = true }, name="post_confirm_token")
     * @Method({"POST"})
     */
    public function confirmationAction(Request $request, $token)
    {
        $restfulJson = new RestfulJsonResponse();

        /** @var $user User */
        $user = $this->getDoctrine()->getRepository('core.user_repository')->findOneBy(array('confirmationToken' => $token));
        if (!$user) {
            $restfulJson->setMessage("Unknown Confirmation Token");
            $restfulJson->setStatusCode(400);
            return $restfulJson;
        }
        $user->setConfirmationToken(null);
        $user->setConfirmed(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $restfulJson->setMessage("Confirmation Token is Valid");
        return $restfulJson;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/auth/status", options = { "expose" = true }, name="get_user_status")
     * @Method({"GET"})
     */
    public function getUserStatusAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $restfulJson = new RestfulJsonResponse();
        $restfulJson->setMessage("User Status");
        $restfulJson->setData([
            "email" => $user->getEmail(),
            "last_login" => $user->getLastLogin(),
            "username" => $user->getUsername(),
            "created_at" => $user->getCreatedAt(),
            "updated_at" => $user->getUpdatedAt(),
            "roles" => $user->getRoles()
        ]);

        return $restfulJson;

    }


}