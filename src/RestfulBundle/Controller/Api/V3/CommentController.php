<?php

namespace RestfulBundle\Controller\Api\V3;

use Codeception\Module\REST;
use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Comment;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\RestfulEnvelope;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');


        $em = $this->getDoctrine()->getManager();

        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var Comment $comment */
        if ($comment = $commentRepository->getCommentByToken($token)) {
            $this->denyAccessUnlessGranted('edit', $comment);

            $comment->setContent($request->get("content"));

            $errors = $validator->validate($comment);
            if ($errors->count() == 0) {
                $em->persist($comment);
                $em->flush();

                return RestfulEnvelope::successResponseTemplate('Comment Saved', $comment,
                    [new UserNormalizer(), new CommentNormalizer()])->response();
            }
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->response();
    }
}
