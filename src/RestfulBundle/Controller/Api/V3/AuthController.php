<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace RestfulBundle\Controller\Api\V3;
use RestfulBundle\Validation\PasswordType;
use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\User;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\AccountNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\CacheService;
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
     * @param User $user
     * @param string $token
     */
    private function confirmationEmail($user,$token)
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
     * @param User $user
     * @param string $token
     */
    private function confirmPasswordResetEmail($user,$token)
    {
        $message = new Swift_Message();
        $message->setSubject('Welcome')
            ->setFrom($user->getEmail())
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'email/reset-password.html.twig',
                    array('user' => $user,'token' => $token)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);
    }

    /**
     * @Route("/auth/register", options = { "expose" = true }, name="post_register")
     * @Method({"POST"})
     */
    public function postRegisterAction(Request $request)
    {
        /** @var CacheService $cacheService */
        $cacheService = $this->get('core.cache_service');

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


        //create a confirmation token
        $token = substr(bin2hex(random_bytes(20)),20);
        $cacheService->setNamespace('user_keys_confirm');
        $cacheService->save($token,$user,1000);
        $this->confirmationEmail($user,$token);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->restful([new WrapperNormalizer(),new UserNormalizer()],new SuccessWrapper($user,"User Registed"));
    }


    /**
     * @Route("/auth/reset-password/{token}/request", options = { "expose" = true }, name="post_password_reset_token")
     * @Method({"POST"})
     */
    public function postRequestRestPasswordAction(Request $request,$token)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get('core.user_repository');

        /** @var CacheService $cacheService */
        $cacheService = $this->get('core.cache_service');

        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        if($user == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);

        $token = substr(bin2hex(random_bytes(20)),20);
        $cacheService->setNamespace("user_key_reset_password");
        $cacheService->save($token,$user,1000);

        $this->confirmPasswordResetEmail($user,$token);
        return $this->restful([new WrapperNormalizer()],new SuccessWrapper("New password reset token sent"));

    }

    /**
     * @Route("/auth/reset-password/{token}", options = { "expose" = true }, name="post_password_reset_token")
     * @Method({"POST"})
     */
    public function postNewPasswordAction(Request $request,$token)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get('core.user_repository');

        /** @var CacheService $cacheService */
        $cacheService = $this->get('core.cache_service');

        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        if($user == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);

        $cacheService->setNamespace('user_key_reset_password');
        /** @var User $cachedUser */
        if($cachedUser = $cacheService->fetch($token))
        {
           if($cachedUser->getId() == $user->getId())
           {
               $passwordType = new PasswordType($request->get("password"));

               $errors = $this->validateEntity($passwordType );
               if($errors->count() > 0)
               {
                   $errorWrapper = new ErrorWrapper("Couldn't Change Password User");
                   $errorWrapper->addErrors($errors);
                   return $this->restful([new WrapperNormalizer()],$errorWrapper, 400);
               }

               $password = $this->get('security.password_encoder')
                   ->encodePassword($user, $passwordType->getPassword());
               $user->setPassword($password);

               $em = $this->getDoctrine()->getManager();
               $em->persist($user);
               $em->flush();
               return $this->restful([new WrapperNormalizer()],new SuccessWrapper("Password Changed"));

           }
        }
        return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);
    }


    /**
     * @Route("/auth/confirm/{token}/request", options = { "expose" = true }, name="post_confirm_token")
     * @Method({"POST"})
     */
    public function postRequestConfirmationAction(Request $request, $token)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get('core.user_repository');

        /** @var CacheService $cacheService */
        $cacheService = $this->get('core.cache_service');

        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        if($user == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);

        //create a confirmation token
        $token = substr(bin2hex(random_bytes(20)),20);
        $cacheService->setNamespace('user_keys_confirm');
        $cacheService->save($token,$user,1000);

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

        /** @var CacheService $cacheService */
        $cacheService = $this->get('core.cache_service');

        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        if($user == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown User"),410);

        $cacheService->setNamespace('user_keys_confirm');
        $tokenUser = $cacheService->fetch($confirmToken);
        if($tokenUser)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown Token"),410);

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