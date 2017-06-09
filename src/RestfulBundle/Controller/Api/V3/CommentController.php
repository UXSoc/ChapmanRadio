<?php

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Comment;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Repository\CommentRepository;
use RestfulBundle\Validation\CommentType;
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
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');


        $em = $this->getDoctrine()->getManager();

        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var Comment $comment */
        if ($comment = $commentRepository->getCommentByToken($token)) {
            $this->denyAccessUnlessGranted('edit', $comment);

            $form = $this->createForm(CommentType::class,$comment);
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em->persist($comment);
                $em->flush();

                return RestfulEnvelope::successResponseTemplate('Comment Saved', $comment,
                    [new UserNormalizer(), new CommentNormalizer()])->response();
            }

            return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(400)->addErrors($form->getErrors())->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown comment")->setStatus(410)->response();
    }
}
