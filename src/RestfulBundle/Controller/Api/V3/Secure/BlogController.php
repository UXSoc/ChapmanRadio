<?php

namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Category;
use CoreBundle\Entity\Image;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Tag;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\RestfulEnvelope;
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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v3/private")
 */
class BlogController extends Controller
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
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        $post = new Post();
        $post->setPinned($request->get('pinned'));
        $post->setContent($request->get('content'));
        $post->setSlug($request->get('slug', $request->get('name')));
        $post->setExcerpt($request->get('excerpt'));
        $post->setAuthor($this->getUser());
        $post->setName($request->get('name'));


        $errors = $validator->validate($post);
        if ($errors->count() > 0)
            return RestfulEnvelope::errorResponseTemplate('Invalid post')->addErrors($errors)->response();

        $em->persist($post);
        $em->flush();

        return RestfulEnvelope::successResponseTemplate("Post created",$post,[new BlogNormalizer(),new UserNormalizer()])->response();
    }


    /**
     * @Route("/post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="patch_post")
     * @Method({"PATCH"})
     */
    public function patchPostAction(Request $request, $token, $slug)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

            $post->setContent($request->get("content", $post->getContent()));
            $post->setName($request->get("name", $post->getName()));
            $post->setSlug($request->get("slug", $post->getSlug()));
            $post->setExcerpt($request->get("excerpt", $post->getExcerpt()));
            $post->setPinned($request->get("pinned", $post->isPinned()));
            $errors = $validator->validate($post);
            if ($errors->count() > 0)
                return RestfulEnvelope::errorResponseTemplate('Invalid post')->addErrors($errors)->response();

            $em->persist($post);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate("Post updated",$post,[new BlogNormalizer(),new UserNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Invalid post')->response();

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
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);
            $em->remove($post);
            $em->flush();

            return RestfulEnvelope::successResponseTemplate('Bost post deleted',$post,[new BlogNormalizer(),new UserNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();

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
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($post->getTags()->containsKey($tag))
                return RestfulEnvelope::errorResponseTemplate('duplicate Tag')->response();

            /** @var TagRepository $tagRepository */
            $tagRepository = $this->get(Tag::class);

            $tag = $tagRepository->getOrCreateTag($tag);
            $em->persist($tag);
            $post->addTag($tag);
            $em->persist($post);
            $em->flush();

            return RestfulEnvelope::successResponseTemplate('Tag added',$tag,[new TagNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();
    }


    /**
     * @Route("/post/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="put_image_post")
     * @Method({"PUT"})
     */
    public function putImageForPostAction(Request $request, $token, $slug)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        /** @var Post $post */
        if ( $post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

            $src = $request->files->get('image', null);
            $image = new Image();
            $image->setImage($src);
            $image->setAuthor($this->getUser());

            $errors = $validator->validate($image);
            if($errors->count() > 0)
                return RestfulEnvelope::errorResponseTemplate('invalid Image')->addErrors($errors)->response();

            $imageService->saveImage($image);
            $em->persist($image);

            $post->addImage($image);
            $em->persist($post);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Image Uploaded',$image,[new ImageNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Image error')->response();
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
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            return RestfulEnvelope::successResponseTemplate('Image Uploaded',$post->getImages()->toArray(),
                [new ImageNormalizer(),new TagNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();
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

        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($t = $post->removeTag($tag)) {
                $em->persist($post);
                $em->flush();
                return RestfulEnvelope::successResponseTemplate('Tag deleted', $t,
                    [new TagNormalizer()])->response();
            }
            return RestfulEnvelope::errorResponseTemplate('Tag not found')->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();

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
        if($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($post->getCategories()->containsKey($category))
                return RestfulEnvelope::errorResponseTemplate('duplicate Tag')->response();

            /** @var CategoryRepository $categoryRepository */
            $categoryRepository = $em->getRepository(Category::class);

            $category = $categoryRepository->getOrCreateCategory($category);
            $em->persist($category);
            $post->addCategory($category);
            $em->persist($post);
            $em->flush();

            return RestfulEnvelope::successResponseTemplate('Category added',$category,[new CategoryNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();

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
        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
            if($c = $post->removeCategory($category)) {
                $em->persist($post);
                $em->flush();
                return RestfulEnvelope::successResponseTemplate('Category deleted', $c,
                    [new CategoryNormalizer()])->response();
            }
            return RestfulEnvelope::errorResponseTemplate('Category not found')->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();

    }
}

