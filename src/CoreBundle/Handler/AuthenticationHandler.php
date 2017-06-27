<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Handler;

use CoreBundle\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;



class AuthenticationHandler implements AuthenticationEntryPointInterface,AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
    private $router;
    private $session;
    private $registry;
    private $handler;

    /**
     * AuthenticationHandler constructor.
     * @param RouterInterface $router
     * @param SessionInterface $session
     * @param RegistryInterface $registry
     */
    public function __construct(RouterInterface $router, SessionInterface $session, ManagerRegistry $registry,ViewHandlerInterface $handler)
    {
        $this->router = $router;
        $this->session = $session;
        $this->registry = $registry;
        $this->handler = $handler;
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->handler->handle(View::create(['message' => "Failed Authentication",'code' => 400],400,[]));

    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        /** @var User $user */
        $user = $token->getUser();
        $user->updateLastLogin();

        $em = $this->registry->getManager();
        $em->persist($user);
        $em->flush();

        return $this->handler->handle(View::create(['message' => "Authentication Success"],200,[]));
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
        if($authException instanceof InsufficientAuthenticationException) {
            return $this->handler->handle(View::create([],403,[]));
        }
        return $this->handler->handle(View::create([],401,[]));
    }
}