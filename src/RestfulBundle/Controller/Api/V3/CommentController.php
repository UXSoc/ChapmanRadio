<?php

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Comment;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v3/")
 */
class CommentController extends Controller
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

        /** @var Comment $comment */
        if ($comment = $commentRepository->getCommentByToken($token)) {
            $this->denyAccessUnlessGranted('edit', $comment);

            $form = $this->createForm(UserType::class,$comment);
            $form->submit($request->request->all());
            if($form->isValid())
            {
                $em->persist($comment);
                $em->flush();

                return RestfulEnvelope::successResponseTemplate('Comment Saved', $comment,
                    [new UserNormalizer(), new CommentNormalizer()])->response();
            }

            return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->addFormErrors($form)->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->response();
    }
}
