<?php

namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Category;
use CoreBundle\Entity\Image;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Tag;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\BlogNormalizer;
use CoreBundle\Normalizer\CategoryNormalizer;
use CoreBundle\Normalizer\ImageNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\CategoryRepository;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\PostVoter;
use CoreBundle\Service\ImageUploadService;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v3/private")
 */
class BlogController extends BaseController
{


    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/post",
     *     options = { "expose" = true },
     *     name="put_post")
     * @Method({"PUT"})
     */
    public function putPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $post = new Post();
        $post->setPinned($request->get('pinned'));
        $post->setContent($request->get('content'));
        $post->setSlug($request->get('slug', $request->get('name')));
        $post->setExcerpt($request->get('excerpt'));
        $post->setAuthor($this->getUser());
        $post->setName($request->get('name'));

        $errors = $this->validateEntity($post);
        if ($errors->count() > 0) {
            $error = new ErrorWrapper();
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()], $error, 400);
        }

        $em->persist($post);
        $em->flush();
        return $this->restful([
            new WrapperNormalizer(),
            new BlogNormalizer(),
            new UserNormalizer()
        ], new SuccessWrapper($post, "Post Created"));

    }


    /**
     * @Route("/post/{token}/{slug}",
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
        $post = $postRepository->getPostByTokenAndSlug($token, $slug);

        if ($post == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 403);
        }

        $post->setContent($request->get("content", $post->getContent()));
        $post->setName($request->get("name", $post->getName()));
        $post->setSlug($request->get("slug", $post->getSlug()));
        $post->setExcerpt($request->get("excerpt", $post->getExcerpt()));
        $post->setPinned($request->get("pinned", $post->isPinned()));

        $errors = $this->validateEntity($post);
        if ($errors->count() > 0) {
            $error = new ErrorWrapper(null);
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()], $error, 400);
        }

        return $this->restful([
            new BlogNormalizer(),
            new UserNormalizer(),
            new PaginatorNormalizer(),
            new WrapperNormalizer()], new SuccessWrapper($post));

    }


    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="delete_post")
     * @Method({"DELETE"})
     */
    public function deletePostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token, $slug);

        if ($post == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);
        try {
            $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }
        $em->remove($post);
        $em->flush();

        return $this->restful([new WrapperNormalizer()], new SuccessWrapper(null, "Blog Post Deleted"));

    }
    //----------------------------------------------------------------------------------------

    /**
     * @Route("/post/{token}/{slug}/tag/{tag}",
     *     options = { "expose" = true },
     *     name="put_tag_post")
     * @Method({"PUT"})
     */
    public function putTagForPostAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token, $slug);

        if ($post == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }
        if ($post->getTags()->containsKey($tag))
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Duplicate Tag Found"), 400);

        /** @var TagRepository $tagRepository */
        $tagRepository = $this->get(Tag::class);
        $tag = $tagRepository->getOrCreateTag($tag);
        $em->persist($tag);
        $post->addTag($tag);

        $em->persist($post);
        $em->flush();

        return $this->restful([new WrapperNormalizer()], new SuccessWrapper(null, "Tag added"));

    }


    /**
     * @Route("/post/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="put_image_post")
     * @Method({"PUT"})
     */
    public function putImageForPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token, $slug);

        if ($post == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        $src = $request->files->get('image', null);
        $image = new Image();
        $image->setImage($src);
        $image->setAuthor($this->getUser());

        $errors = $this->validateEntity($image);
        if ($errors->count() > 0) {
            $error = new ErrorWrapper(null);
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()], $error, 400);
        }
        $imageService->saveImage($image);
        $em->persist($image);

        $post->addImage($image);
        $em->persist($post);
        $em->flush();

        return $this->restful([new WrapperNormalizer()], new SuccessWrapper(null, "Image Uploaded"));
    }

    /**
     * @Route("/post/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="get_image_post")
     * @Method({"GET"})
     */
    public function getImageForPostAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token, $slug);

        if ($post == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        return $this->restful([
            new WrapperNormalizer(),
            new TagNormalizer(),
            new ImageNormalizer()], new SuccessWrapper($post->getImages()->toArray(), "Images"));

    }


    /**
     * @Route("/post/{token}/{slug}/tag/{tag}", options = { "expose" = true }, name="delete_tag_post")
     * @Method({"DELETE"})
     */
    public function deleteTagForPostAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token, $slug);

        if ($post == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        $result = $post->removeTag($tag);
        if ($result == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Does Not Have Tag"), 410);

        $em->persist($post);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new TagNormalizer()], new SuccessWrapper($result, "Tag Deleted"));

    }


    /**
     * @Route("/post/{token}/{slug}/category/{category}", options = { "expose" = true }, name="put_category_post")
     * @Method({"PUT"})
     */
    public function putCategoryForPostAction(Request $request, $token, $slug, $category)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token, $slug);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);
        if ($post == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        $category = $categoryRepository->getOrCreateCategory($category);
        $errors = $this->validateEntity($category);
        if ($errors->count() > 0) {
            $error = new ErrorWrapper("Invalid Tag");
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()], $error, 410);
        }
        $em->persist($category);

        $post->addCategory($category);
        $em->persist($post);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new CategoryNormalizer()], new SuccessWrapper($category, "Tag Deleted"));
    }

    /**
     * @Route("/post/{token}/{slug}/category/{category}",
     *     options = { "expose" = true },
     *     name="delete_category_post")
     * @Method({"DELETE"})
     */
    public function deleteCategoryForPostAction(Request $request, $token, $slug, $category)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token, $slug);

        if ($post == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        $result = $post->removeCategory($category);
        if ($result == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Does Not Have Tag"), 410);

        $em->persist($post);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new TagNormalizer()], new SuccessWrapper($result, "Tag Deleted"));

    }
}
