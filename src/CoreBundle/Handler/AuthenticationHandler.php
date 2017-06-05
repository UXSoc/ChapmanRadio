<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Handler;

use CoreBundle\Entity\User;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Serializer\Serializer;


class AuthenticationHandler implements AuthenticationEntryPointInterface,AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
    private $router;
    private $session;
    private $registry;

    /**
     * AuthenticationHandler constructor.
     * @param RouterInterface $router
     * @param SessionInterface $session
     * @param RegistryInterface $registry
     */
    public function __construct(RouterInterface $router, SessionInterface $session, RegistryInterface $registry)
    {
        $this->router = $router;
        $this->session = $session;
        $this->registry = $registry;
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     *
     * @return JsonResponse The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $normalizer =  new Serializer([new WrapperNormalizer()]);
        return new JsonResponse($normalizer->normalize(new ErrorWrapper("Failed Authentication")),400);

    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        /** @var User $user */
        $user = $token->getUser();
        $user->updateLastLogin();

        $em = $this->registry->getManager();
        $em->persist($user);
        $em->flush();

        $normalizer =  new Serializer([new WrapperNormalizer(),new UserNormalizer()]);
        return new JsonResponse($normalizer->normalize(new SuccessWrapper($token->getUser(),"Authenticated Successful")),200);
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * This is called when an anonymous request accesses a resource that
     * requires authentication. The job of this method is to return some
     * response that "helps" the user start into the authentication process.
     *
     * Examples:
     *  A) For a form login, you might redirect to the login page
     *      return new RedirectResponse('/login');
     *  B) For an API token authentication system, you return a 401 response
     *      return new Response('Auth header required', 401);
     *
     * @param Request $request The request that resulted in an AuthenticationException
     * @param AuthenticationException $authException The exception that started the authentication process
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $normalizer =  new Serializer([new WrapperNormalizer()]);
        if($authException instanceof InsufficientAuthenticationException) {
            return new JsonResponse($normalizer->normalize(new ErrorWrapper("Permission Error")),400);
        }
        return new JsonResponse($normalizer->normalize(new ErrorWrapper("Authentication Error")),400);
    }
}