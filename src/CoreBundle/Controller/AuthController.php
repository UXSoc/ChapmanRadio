<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Controller;

use CoreBundle\Event\UserEvent;
use CoreBundle\Events;
use CoreBundle\Form\UserType;
use CoreBundle\Service\UserTokenService;
use FOS\RestBundle\Controller\FOSRestController;
use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/api/v3")
 */
class AuthController extends FOSRestController
{
    /**
     * @Route("/auth/register",
     *     options = { "expose" = true },
     *     name="post_register")
     * @Method({"POST"})
     */
    public function postRegisterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainTextPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
        }
        return $this->view($form);
    }


    /**
     * @Route("/auth/reset-password/{token}/request", options = { "expose" = true }, name="post_password_reset_token")
     * @Method({"POST"})
     */
    public function postRequestRestPasswordAction(Request $request,$token)
    {
        $em = $this->getDoctrine();
        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository(User::class);

        /** @var User $user */
        if($user = $userRepository->getByToken($token))
        {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(Events::USER_PASSWORD_RESET,new UserEvent($user));
            return $this->view();
        }
        throw $this->createNotFoundException('User Not Found');

    }

    /**
     * @Route("/auth/reset-password/{token}/{confirmationToken}", options = { "expose" = true }, name="post_password_reset_token")
     * @Method({"POST"})
     */
    public function postNewPasswordAction(Request $request,$token,$confirmationToken)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var UserTokenService $userTokenService */
        $userTokenService = $this->get(UserTokenService::class);

        /** @var UserRepository $userRepository */
        $userRepository = $this->get(User::class);

        /** @var User $user */
        if($user = $userRepository->getByToken($token))
        {
            /** @var User $userToken */
            if($userToken = $userTokenService->verifyPasswordResetToken($confirmationToken))
            {
                if($userToken->getId() === $user->getId())
                    throw $this->createNotFoundException('Unknown User');

                $form = $this->createFormBuilder()
                    ->add('password',PasswordType::class,array())
                    ->getForm();

                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid())
                {
                    $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $form->getData()['password']);
                    $user->setPassword($password);
                    $em->persist($user);
                    $em->flush();
                }
                return $this->view($form);
            }
        }
        throw $this->createNotFoundException('Unkown User');
    }


    /**
     * @Route("/auth/confirm/{token}/request", options = { "expose" = true }, name="post_confirm_token")
     * @Method({"POST"})
     */
    public function postRequestConfirmationAction(Request $request, $token)
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');

        /** @var UserRepository $userRepository */
        $userRepository = $this->get(User::class);

        /** @var User $user */
        if($user = $userRepository->getByToken($token))
        {
            $dispatcher->dispatch(Events::USER_CONFIRMATION,new UserEvent($user));
            return $this->view([]);
        }
        throw $this->createNotFoundException("User Not Found");
    }

    /**
     * @Route("/auth/confirm/{token}/{confirmToken}", options = { "expose" = true }, name="post_confirm_token")
     * @Method({"POST"})
     */
    public function postConfirmationAction(Request $request, $token,$confirmToken)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get(User::class);

        /** @var UserTokenService $userTokenService */
        $userTokenService = $this->get(UserTokenService::class);

        /** @var User $user */
        if($user = $userRepository->getByToken($token))
        {
            /** @var User $cacheItem */
            if($user2 = $userTokenService->verifyConfirmationToken($confirmToken))
            {
                if($user->getId() == $user2->getId())
                {
                    $user->setConfirmed(true);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    return $this->view([]);
                }
            }
        }
        throw $this->createNotFoundException("Not Found User");
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Rest\Get("/user/me", options = { "expose" = true }, name="get_user_status")
     */
    public function getUserStatusAction(Request $request)
    {
        return $this->view(['user' => $this->getUser()]);
    }

}