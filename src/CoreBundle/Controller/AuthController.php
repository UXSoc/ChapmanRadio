<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Controller;


use CoreBundle\Event\UserEvent;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Service\UserTokenService;
use RestfulBundle\Validation\PasswordType;
use CoreBundle\Entity\User;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v3")
 */
class AuthController extends Controller
{

    /**
     * @Route("/auth/register",
     *     options = { "expose" = true },
     *     name="post_register")
     * @Method({"POST"})
     */
    public function postRegisterAction(Request $request)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');

        $user = new User();
        $user->setName($request->get("name"));
        $user->setUsername($request->get("username"));
        $user->setEmail($request->get("email"));
        $passwordType = new PasswordType($request->get("password"));
        $user->setStudentId($request->get("studentId"));

        $errors = $validator->validate($user);
        if($errors->count() > 0)
            return RestfulEnvelope::errorResponseTemplate("Couldn't Register User")
                ->addErrors($errors)
                ->response();


        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $passwordType->getPassword());
        $user->setPassword($password);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $dispatcher->dispatch('user.confirmation',new UserEvent($user));
        return RestfulEnvelope::successResponseTemplate('User registered',$user,[new UserNormalizer()]);
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
        {        /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch('user.password_reset',new UserEvent($user));

            return RestfulEnvelope::successResponseTemplate('New password reset token sent');
        }

        return RestfulEnvelope::errorResponseTemplate("Unknown User")
            ->setStatus(410)
            ->response();

    }

    /**
     * @Route("/auth/reset-password/{token}/{confirmationToken}", options = { "expose" = true }, name="post_password_reset_token")
     * @Method({"POST"})
     */
    public function postNewPasswordAction(Request $request,$token,$confirmationToken)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

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
                if($userToken->getId() == $user->getId())
                    return RestfulEnvelope::errorResponseTemplate("Unknown User")
                        ->setStatus(410)
                        ->response();

                $passwordType = new PasswordType($request->get("password"));
                $errors = $validator->validate($passwordType);
                if($errors->count() > 0)
                    return RestfulEnvelope::errorResponseTemplate("Couldn't reset password")
                        ->addErrors($errors)
                        ->response();

                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $passwordType->getPassword());
                $user->setPassword($password);

                $em->persist($user);
                $em->flush();

                return RestfulEnvelope::successResponseTemplate('Password Changed');
            }
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown User")
            ->setStatus(410)
            ->response();
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
            $dispatcher->dispatch('user.confirmation',new UserEvent($user));
            return RestfulEnvelope::successResponseTemplate('New confirmation token sent')->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown User")->response();
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
                    return RestfulEnvelope::successResponseTemplate('User Confirmed')->response();
                }
            }
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown User")->response();
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/user/me", options = { "expose" = true }, name="get_user_status")
     * @Method({"GET"})
     */
    public function getUserStatusAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        return RestfulEnvelope::successResponseTemplate('Account status',$user,[new UserNormalizer()]);
    }

}