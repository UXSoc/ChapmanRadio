<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller\Api\V3;
use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\User;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\AccountNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\UserService;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        /** @var UserService $userService */
        $userService = $this->get('core.user_service');

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


        //create a confirmation token
        $token = $userService->createConfirmationToken($user);
        $this->confirmationEmail($user,$token);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->restful([new WrapperNormalizer()],new SuccessWrapper($user,"User Registed"));
    }

    /**
     * @param User $user
     * @param string $token
     */
    public function confirmationEmail($user,$token)
    {
        $message = new Swift_Message();
        $message->setSubject('Welcome')
            ->setFrom($user->getEmail())
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'email/confirm.html.twig',
                    array('user' => $user,'token' => $token)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);
    }

    /**
     * @Route("/auth/confirm/{token}/request", options = { "expose" = true }, name="post_confirm_token")
     * @Method({"POST"})
     */
    public function postRequestConfirmationAction(Request $request, $token,$confirmToken)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get('core.user_repository');
        /** @var UserService $userService */
        $userService = $this->get('core.user_service');

        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        if($user == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);

        //create a confirmation token
        $token = $userService->createConfirmationToken($user);
        $this->confirmationEmail($user,$token);

        return $this->restful([new WrapperNormalizer()],new SuccessWrapper("New confirmation token sent"));
    }

    /**
     * @Route("/auth/confirm/{token}/{confirmToken}", options = { "expose" = true }, name="post_confirm_token")
     * @Method({"POST"})
     */
    public function postConfirmationAction(Request $request, $token,$confirmToken)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get('core.user_repository');
        /** @var UserService $userService */
        $userService = $this->get('core.user_service');

        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        if($user == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);

        $result = $userService->verifyConfirmationToken($confirmToken);
        if($result == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown Token"),410);

        /** @var User $tokenUser */
        $tokenUser = $userRepository->findOneBy(['token' => $result]);
        if($tokenUser == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);

        if($tokenUser->getId() == $user->getId())
        {
            $user->setConfirmed(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->restful([new WrapperNormalizer(),new AccountNormalizer()],new SuccessWrapper($user,"User Confirmed"));
        }

        return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown Token"),410);
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