<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Controller;


use CoreBundle\Event\AuthSubscriber;
use CoreBundle\Event\UserEvent;
use CoreBundle\Service\RestfulService;
use CoreBundle\Service\UserTokenService;
use Monolog\Logger;
use RestfulBundle\Validation\PasswordType;
use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\User;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\AccountNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\UserRepository;
use Swift_Message;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
     * @Route("/auth/register",
     *     options = { "expose" = true },
     *     name="post_register")
     * @Method({"POST"})
     */
    public function postRegisterAction(Request $request)
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');

        $user = new User();
        $user->setName($request->get("name"));
        $user->setUsername($request->get("username"));
        $user->setEmail($request->get("email"));
        $passwordType = new PasswordType($request->get("password"));
        $user->setStudentId($request->get("studentId"));

        $errors = $this->validateEntity($user);
        $passwordErrors = $this->validateEntity($passwordType);
        if ($errors->count() > 0 | $passwordErrors->count()) {
            $errorWrapper = new ErrorWrapper("Couldn't Register User");
            $errorWrapper->addErrors($errors);
            $errorWrapper->addErrors($passwordErrors);
            return $this->restful([new WrapperNormalizer()],$errorWrapper, 400);
        }

        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $passwordType->getPassword());
        $user->setPassword($password);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $dispatcher->dispatch('user.confirmation',new UserEvent($user));
        return $this->restful([new WrapperNormalizer(),new UserNormalizer()],new SuccessWrapper($user,"User Registed"));
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

        /** @var RestfulService $restfulService */
        $restfulService = $this->get(RestfulService::class);

        /** @var User $user */
        if($user = $userRepository->getByToken($token))
            return $restfulService->errorResponse('Unknown User',410);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch('user.password_reset',new UserEvent($user));

        return $restfulService->successResponse([],null,"New password reset token sent");

    }

    /**
     * @Route("/auth/reset-password/{token}/{confirmationToken}", options = { "expose" = true }, name="post_password_reset_token")
     * @Method({"POST"})
     */
    public function postNewPasswordAction(Request $request,$token,$confirmationToken)
    {
        /** @var UserTokenService $userTokenService */
        $userTokenService = $this->get(UserTokenService::class);

        /** @var RestfulService $restfulService */
        $restfulService = $this->get(RestfulService::class);

        /** @var UserRepository $userRepository */
        $userRepository = $this->get(User::class);

        /** @var User $user */
        if($user = $userRepository->getByToken($token))
            return $restfulService->errorResponse('Unknown User',410);

        /** @var User $userToken */
        if($userToken = $userTokenService->verifyPasswordResetToken($confirmationToken))
        {
            if($userToken->getId() == $user->getId())
                return $restfulService->errorResponse('Unknown User',410);

            $passwordType = new PasswordType($request->get("password"));

            if($resp = $restfulService->errorResponseValidate($passwordType,"Couldn't Change Password User"))
                return $resp;

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $passwordType->getPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $restfulService->successResponse([],null,"Password Changed");
        }
    }


    /**
     * @Route("/auth/confirm/{token}/request", options = { "expose" = true }, name="post_confirm_token")
     * @Method({"POST"})
     */
    public function postRequestConfirmationAction(Request $request, $token)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get(User::class);

        /** @var User $user */
        if($user = $userRepository->getByToken($token))
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch('user.confirmation',new UserEvent($user));

        return $this->restful([new WrapperNormalizer()],new SuccessWrapper("New confirmation token sent"));
    }

    /**
     * @Route("/auth/confirm/{token}/{confirmToken}", options = { "expose" = true }, name="post_confirm_token")
     * @Method({"POST"})
     */
    public function postConfirmationAction(Request $request, $token,$confirmToken)
    {
        /** @var RestfulService $restfulService */
        $restfulService = $this->get(RestfulService::class);

        /** @var UserRepository $userRepository */
        $userRepository = $this->get(User::class);

        /** @var UserTokenService $userTokenService */
        $userTokenService = $this->get(UserTokenService::class);

        /** @var User $user */
        if($user = $userRepository->getByToken($token))
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);


        /** @var User $cacheItem */
        if($user2 = $userTokenService->verifyConfirmationToken($confirmToken))
        {
            if($user->getId() == $user2->getId())
            {
                $user->setConfirmed(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return $restfulService->successResponse([new AccountNormalizer()],$user,"User Confirmed");
            }
        }
        return $restfulService->errorResponse("Unknown Token",410);
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

        return $this->restful([
            new WrapperNormalizer(),
            new AccountNormalizer()
        ],new SuccessWrapper($user,"Account status"));
    }

}