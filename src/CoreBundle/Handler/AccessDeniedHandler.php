<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 6:47 PM.
 */

namespace CoreBundle\Handler;

use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Normalizer\WrapperNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Serializer\Serializer;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * Handles an access denied failure.
     *
     * @param Request               $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return Response may return null
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $normalizer = new Serializer([new WrapperNormalizer()]);

        return new JsonResponse($normalizer->normalize(new ErrorWrapper('Access Denied')), 403);
    }
}
