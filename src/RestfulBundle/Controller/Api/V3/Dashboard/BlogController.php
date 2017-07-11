<?php
namespace RestfulBundle\Controller\Api\V3\Dashboard;


use CoreBundle\Entity\Category;
use CoreBundle\Entity\Image;
use CoreBundle\Entity\Media;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\PostMeta;
use CoreBundle\Entity\Tag;
use CoreBundle\Event\MediaSaveEvent;
use CoreBundle\Form\FeaturePostMediaType;
use CoreBundle\Form\MediaType;
use CoreBundle\Form\PostType;
use CoreBundle\Helper\MediaFilterBuilder;
use CoreBundle\Repository\CategoryRepository;
use CoreBundle\Repository\MediaRepository;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\PostVoter;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/api/v3/")
 */
class BlogController extends FOSRestController
{

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Get("post/datatable",
     *     options = { "expose" = true },
     *     name="get_post_dataTable")
     */
    public function getPostDatatableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PostRepository $postRepo */
        $postRepo = $em->getRepository(Post::class);
        return $this->view(['datatable' => $postRepo->dataTableFilter($request)]);
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Post("post",
     *     options = { "expose" = true },
     *     name="post_post")
     * @Rest\View(serializerGroups={"detail"})
     */
    public function postPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();

        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $post->setDeltaRenderer('delta');
            $post->setAuthor($this->getUser());
            $em->persist($post);
            $em->flush();
            return $this->view(['post' => $post]);
        }
        return $this->view($form);
    }



    /**
     * @Rest\Patch("post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="patch_post")
     * @Rest\View(serializerGroups={"detail"})
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

            $form = $this->createForm(PostType::class,$post,['method' => 'patch']);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $post->setDeltaRenderer('delta');
                $em->persist($post);
                $em->flush();
                return $this->view(['post' => $post]);
            }
            return $this->view($form);
        }
        throw $this->createNotFoundException("Post Not Found");
    }




    /**
     * @Rest\Put("post/{token}/{slug}/tag/{tag}",
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

            /** @var TagRepository $tagRepository */
            $tagRepository = $em->getRepository(Tag::class);

            $tag = $tagRepository->getOrCreateTag($tag);

            if($post->hasTag($tag))
                return new HttpException(400,'Duplicate Tag');

            $em->persist($tag);
            $post->addTag($tag);
            $em->persist($post);
            $em->flush();
            return $this->view(["tag" => $tag->getTag()]);
        }
        throw $this->createNotFoundException('Post Not Found');
    }


    /**
     * @Rest\Post("post/{token}/{slug}/media",
     *     options = { "expose" = true },
     *     name="post_media_post")
     */
    public function postPostMediaAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */

        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

            $media = new Media();
            $media->setAuthor($this->getUser());
            $form = $this->createForm(MediaType::class,$media);
            if($form->isSubmitted() && $form->isValid()) {
                /** @var EventDispatcher $dispatcher */
                $dispatcher = $this->get('event_dispatcher');
                $event = new MediaSaveEvent($media);
                $dispatcher->dispatch(MediaSaveEvent::NAME,$event);

                $post->addMedia($media);
                $em->persist($media);
                $em->persist($post);
                $em->flush();
                return $this->view(['media' => $media]);
            }
            return $this->view($form);
        }
        throw  $this->createNotFoundException('Media Not Found');

    }

    /**
     * @Rest\Post("post/{token}/{slug}/media/feature",
     *     options = { "expose" = true },
     *     name="post_image_post")
     */
    public function postPostFeatureImages(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $em->getRepository(Post::class);
        /** @var Post $post */

        if ($post = $postRepository->getPostByTokenAndSlug($token, $slug))
        {
            $form = $this->createForm(FeaturePostMediaType::class);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $data = $form->getData();

                /** @var MediaRepository $mediaRepository */
                $mediaRepository = $em->getRepository(Media::class);
                /** @var Media $media */
                if($media = $mediaRepository->getMediaByToken($data['mediaToken']))
                {
                   $meta =  $post->getMetaByKey(PostMeta::FEATURE,true);
                   $value = [
                       'mediaToken' => $data['mediaToken'],
                       'square' => (new MediaFilterBuilder())->orignal($media->getFilter())->crop($data['xSquare'],$data['ySquare'],$data['widthWide'],$data['heightWide'])->getResult(),
                       'wide' => (new MediaFilterBuilder())->orignal($media->getFilter())->crop($data['xWide'],$data['yWide'],$data['widthWide'],$data['heightWide'])->getResult(),
                   ];
                   $meta->setValue($value);
                   $em->persist($meta);
                   $em->flush();
                   return $this->view(['post' => $post]);
                }
                throw  $this->createNotFoundException('Media Not Found');
            }
            return $this->view($form);
        }
        throw  $this->createNotFoundException('Post Not Found');
    }


    /**
     * @Rest\Delete("post/{token}/{slug}/tag/{tag}",
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
     * @Rest\Put("post/{token}/{slug}/category/{category}",
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

            /** @var CategoryRepository $categoryRepository */
            $categoryRepository = $em->getRepository(Category::class);

            $category = $categoryRepository->getOrCreateCategory($category);

            if($post->hasCategory($category))
                throw new HttpException(400,"Duplicate Tag");

            $em->persist($category);
            $post->addCategory($category);
            $em->persist($post);
            $em->flush();
            return $this->view(['Category' => $category->getCategory()]);
        }
        throw  $this->createNotFoundException('Post Not Found');

    }

    /**
     * @Rest\Delete("post/{token}/{slug}/category/{category}",
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

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Delete("post/{token}/{slug}",
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
}