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
use CoreBundle\Repository\PostRepository;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

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
        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.comment_repository');

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

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->restful([
            new CommentNormalizer(),
            new UserNormalizer(),
            new WrapperNormalizer()],
            new SuccessWrapper($comment, "Comment Saved"));

    }
}