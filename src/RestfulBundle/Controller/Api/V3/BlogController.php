<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/24/17
 * Time: 10:46 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Post;
use CoreBundle\Entity\Comment;
use CoreBundle\Form\CommentType;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Repository\CommentRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/api/v3/")
 */
class BlogController extends FOSRestController
{

    /**
     * @Rest\Get("post",
     *     options = { "expose" = true },
     *     name="get_posts")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getPostsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        return $this->view(["payload" =>
            $postRepository->paginator($postRepository->filter($request),
                (int)$request->get('page', 0),
                (int)$request->get('perPage', 10), 20)]);
    }

    /**
     * @Rest\Get("post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="get_post", )
     * @Rest\View(serializerGroups={"detail"})
     */
    public function getPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug)) {
            $post->setDeltaRenderer($request->get('delta','HTML'));
            return $this->view([
                "post" => $post
            ], 200);
        }
        throw new NotFoundHttpException("Post Not Found");
    }


    /**
     * @Rest\Get("post/{token}/{slug}/tags",
     *     options = { "expose" = true },
     *     name="get_post_tags")
     * @Rest\View(serializerGroups={"detail"})
     */
    public function getPostTagsAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
            return $this->view(['tags' => $post->getTags()->getValues()]);
        throw $this->createNotFoundException("Post Not Found");
    }

    /**
     * @Rest\Get("post/{token}/{slug}/categories",
     *     options = { "expose" = true },
     *     name="get_post_categories")
     */
    public function getPostCategoriesAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
            return $this->view(['categories' => $post->getCategories()]);
        throw $this->createNotFoundException("Post Not Found");
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Rest\Post("post/{token}/{slug}/comment",
     *     options = { "expose" = true },
     *     name="post_post_comment")
     *  @Rest\View(serializerGroups={"detail"})
     */
    public function postPostCommentAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug)) {
            $comment = new Comment();
            $comment->setUser($this->getUser());
            $post->addComment($comment);

            $form = $this->createForm(CommentType::class,$comment);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($comment);
                $em->persist($post);
                $em->flush();
                return $this->view(['comment' => $comment]);
            }
            return $this->view($form);
        }
        throw  $this->createNotFoundException("Comment Not Found", 410);
    }


    /**
     * @Rest\Get("post/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="get_blog_comment")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getPostCommentAction(Request $request, $token, $slug, $comment_token = null)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug)) {
            if ($comment_token) {
                return $this->view(["comments" => $commentRepository->getCommentByPostAndToken($post, $comment_token)]);
            } else {
                return $this->view(["comments" => $commentRepository->getAllRootCommentsForPost($post)]);
            }
        }
        throw  $this->createNotFoundException("Unknown Comment");
    }
}
