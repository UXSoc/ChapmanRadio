<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller\Api\V3;

use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\User;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\RestfulHelper;
use CoreBundle\Helper\RestfulJsonResponse;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\AccountNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\UserRepository;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
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
        $user = new User();
        $user->setName($request->get("name"));
        $user->setUsername($request->get("username"));
        $user->setEmail($request->get("email"));
        $user->setPlainPassword($request->get("password"));
        $user->setStudentId($request->get("studentId"));

        $errors = $this->validateEntity($user);
        if ($errors->count() > 0) {
            $errorWrapper = new ErrorWrapper("Couldn't Register User");
            $errorWrapper->addErrors($errors);
            return $this->restful([new WrapperNormalizer()],$errorWrapper, 400);
        }

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

        return $this->restful([new WrapperNormalizer()],new SuccessWrapper($user,"User Registed"));
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

        return $this->restful([
            new AccountNormalizer()
        ],$user);

    }


}