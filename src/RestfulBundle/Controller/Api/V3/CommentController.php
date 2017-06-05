<?php

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Comment;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Service\RestfulService;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * @Route("/api/v3/")
 */
class CommentController extends BaseController
{

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("comment/{token}", options = { "expose" = true }, name="patch_comment")
     * @Method({"PATCH"})
     */
    public function patchCommentAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var RestfulService $restfulService */
        $restfulService = $this->get(RestfulService::class);

        /** @var Comment $comment */
        $comment = null;
        try {
            $comment = $commentRepository->getCommentByToken($token);
        } catch (NoResultException $e) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Unknown Comment"), 410);
        }

        try {
            $this->denyAccessUnlessGranted('edit', $comment);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Comment Permission Error"), 400);
        }

        $comment->setContent($request->get("content"));

        $errors = $this->validateEntity($comment);
        if ($errors->count() > 0) {
            $error = new ErrorWrapper("invalid token");
            $error->addErrors($this->validateEntity($comment));
            $error->setMessage("Invalid Comment");
            return $this->restful([new WrapperNormalizer()], $error, 400);
        }

        $em->persist($comment);
        $em->flush();

        return $restfulService->response([
            new CommentNormalizer(),
            new UserNormalizer(),
            new WrapperNormalizer()],
            new SuccessWrapper($comment, "Comment Saved"));

    }
}
