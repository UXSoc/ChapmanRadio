<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/24/17
 * Time: 10:46 PM
 */

namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Category;
use CoreBundle\Entity\Image;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Tag;
use CoreBundle\Form\CommentType;
use CoreBundle\Form\PostType;
use CoreBundle\Form\ImageType;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Repository\CategoryRepository;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\PostVoter;
use CoreBundle\Service\ImageUploadService;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
            return $this->view(['categories' => $post->getCategories()->getValues()]);
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
        /** @var CommentRepository $commentRepository */
        $commentRepository = $em->getRepository(Comment::class);

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

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Post("/post/datatable",
     *     options = { "expose" = true },
     *     name="get_post_dataTable")
     * @Method({"GET"})
     */
    public function getPostDatatableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepo */
        $postRepo = $em->getRepository(Post::class);
        return $this->view($postRepo->dataTableFilter($request));
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Post("/post",
     *     options = { "expose" = true },
     *     name="post_post")
     */
    public function postPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();

        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($post);
            $em->flush();
        }
        return $this->view($form);
    }


    /**
     * @Rest\Patch("/post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="patch_post")
     * @Method({"PATCH"})
     */
    public function patchPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

            $form = $this->createForm(PostType::class,$post);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($post);
                $em->flush();
            }
            return $this->view($form);
        }
        throw $this->createNotFoundException("Post Not Found");
    }


    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Delete("/post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="delete_post")
     */
    public function deletePostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);
            $em->remove($post);
            $em->flush();
            return $this->view(['post' => $post]);
        }
        throw $this->createNotFoundException("Post Not Found");
    }
    //----------------------------------------------------------------------------------------

    /**
     * @Rest\Put("/post/{token}/{slug}/tag/{tag}",
     *     options = { "expose" = true },
     *     name="put_tag_post")
     */
    public function putTagForPostAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($post->getTags()->containsKey($tag))
                return RestfulEnvelope::errorResponseTemplate('duplicate Tag')->response();

            /** @var TagRepository $tagRepository */
            $tagRepository = $em->getRepository(Tag::class);

            $tag = $tagRepository->getOrCreateTag($tag);
            $em->persist($tag);
            $post->addTag($tag);
            $em->persist($post);
            $em->flush();
            return $this->view(["tag" => $tag->getTag()]);
        }
        throw $this->createNotFoundException('Post Not Found');
    }


    /**
     * @Rest\Post("/post/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="post_image_post")
     */
    public function putImageForPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        /** @var Post $post */
        if ( $post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

            $image = new Image();
            $image->setAuthor($this->getUser());
            $form = $this->createForm(ImageType::class,$image);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $imageService->saveImageToFilesystem($image);
                $em->persist($image);
                $post->addImage($image);
                $em->persist($post);
                $em->flush();
            }
            return $this->view($form);
        }
        throw new BadRequestHttpException('Image Error');
    }

    /**
     * @Rest\Get("/post/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="get_image_post")
     */
    public function getImageForPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            return $this->view($post->getImages());
        }
        throw  $this->createNotFoundException('Post Not Found');
    }


    /**
     * @Rest\Delete("/post/{token}/{slug}/tag/{tag}",
     *     options = { "expose" = true },
     *     name="delete_tag_post")
     */
    public function deleteTagForPostAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */

        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            /** @var Tag $t */
            if($t = $post->removeTag($tag)) {
                $em->persist($post);
                $em->flush();
                return $this->view(['tag' => $t->getTag()]);
            }
            throw  $this->createNotFoundException('Tag Not Found');
        }
        throw  $this->createNotFoundException('Post Not Found');
    }


    /**
     * @Rest\Put("/post/{token}/{slug}/category/{category}",
     *     options = { "expose" = true },
     *     name="put_category_post")
     */
    public function putCategoryForPostAction(Request $request, $token, $slug, $category)
    {

        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($post->getCategories()->containsKey($category))
                throw new HttpException(400,"Duplicate Tag");

            /** @var CategoryRepository $categoryRepository */
            $categoryRepository = $em->getRepository(Category::class);

            $category = $categoryRepository->getOrCreateCategory($category);
            $em->persist($category);
            $post->addCategory($category);
            $em->persist($post);
            $em->flush();
            return $this->view(['Category' => $category->getCategory()]);
        }
        throw  $this->createNotFoundException('Post Not Found');

    }

    /**
     * @Rest\Delete("/post/{token}/{slug}/category/{category}",
     *     options = { "expose" = true },
     *     name="delete_category_post")
     */
    public function deleteCategoryForPostAction(Request $request, $token, $slug, $category)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($c = $post->removeCategory($category)) {
                $em->persist($post);
                $em->flush();
                return $this->view(['Category' => $c]);
            }
            throw  $this->createNotFoundException('Category Not Found');
        }
        throw  $this->createNotFoundException('Post Not Found');
    }

}
