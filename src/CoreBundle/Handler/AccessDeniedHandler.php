<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 6:47 PM
 */

namespace CoreBundle\Handler;



use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;


class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    private $handler;


    public function __construct(ViewHandlerInterface $handler)
    {

        $this->handler = $handler;
    }

    /**
     * Handles an access denied failure.
     *
     * @param Request $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return Response may return null
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        return $this->handler->handle(View::create([],403,[]));
    }
}