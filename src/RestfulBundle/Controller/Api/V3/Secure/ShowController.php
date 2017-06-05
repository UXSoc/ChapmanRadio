<?php

namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\Image;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\Tag;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\ImageNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\ShowRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\ShowVoter;
use CoreBundle\Service\ImageUploadService;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v3/private")
 */
class ShowController extends BaseController
{
    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/show", options = { "expose" = true }, name="put_show")
     * @Method({"PUT"})
     */
    public function putShowAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $show = new Show();
        $show->setName($request->get('name'));
        $show->setSlug($request->get('slug', $request->get('name')));
        $show->setDescription($request->get('description'));
        $show->setEnableComments($request->get('enable_comments'));


        $errors = $this->validateEntity($show);
        if ($errors->count() > 0) {
            $error = new ErrorWrapper();
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()], $error, 400);
        }

        $em->persist($show);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new ShowNormalizer()
        ], new SuccessWrapper($show, "Show Created"));
    }


    /**
     * @Security("has_role('ROLE_DJ')")
     * @Route("/show/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="patch_show")
     * @Method({"PATCH"})
     */
    public function patchShowAction(Request $request, $token, $slug)
    {

        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get(Show::class);

        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token, $slug);
        if ($show == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Show Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Show Permission Error"), 400);
        }

        $show->setName($request->get('name', $show->getName()));
        $show->setSlug($request->get('slug', $show->getSlug()));
        $show->setDescription($request->get('description', $show->getDescription()));
        $show->setEnableComments($request->get('enable_comments', $show->getEnableComments()));

        $errors = $this->validateEntity($show);
        if ($errors->count() > 0) {
            $error = new ErrorWrapper();
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()], $error, 400);
        }

        $em->persist($show);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new ShowNormalizer()
        ], new SuccessWrapper($show, "Show Updated"));


    }


    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("/show/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="delete_show")
     * @Method({"DELETE"})
     */
    public function deleteShowAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get(Show::class);

        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token, $slug);
        if ($show == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Show Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::DELETE, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        $em->remove($show);
        $em->flush();

        return $this->restful([new WrapperNormalizer()], new SuccessWrapper(null, "Show Deleted"));
    }


    /**
     * @Route("/show/{token}/{slug}/tag/{tag}",
     *      options = { "expose" = true },
     *     name="put_tag_post")
     * @Method({"PUT"})
     */
    public function putTagForShowAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);
        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token, $slug);

        if ($show === null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Show Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }
        if ($show->getTags()->containsKey($tag))
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Duplicate Tag Found"), 400);

        /** @var TagRepository $tagRepository */
        $tagRepository = $this->get(Tag::class);
        $tag = $tagRepository->getOrCreateTag($tag);
        $em->persist($tag);
        $show->addTag($tag);

        $em->persist($show);
        $em->flush();

        return $this->restful([new WrapperNormalizer()], new SuccessWrapper(null, "Tag added"));

    }

    /**
     * @Route("/show/{token}/{slug}/tag/{tag}", options = { "expose" = true }, name="delete_tag_post")
     * @Method({"DELETE"})
     */
    public function deleteTagForPostAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);
        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token, $slug);

        if ($show === null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        if ($result = $show->removeTag($tag))
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Does Not Have Tag"), 410);

        $em->persist($show);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new TagNormalizer()], new SuccessWrapper($result, "Tag Deleted"));

    }

    /**
     * @Route("/show/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="put_image_show")
     * @Method({"PUT"})
     */
    public function putImageForShowAction(Request $request, $token, $slug)
    {

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);
        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token, $slug);

        if ($show === null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);
        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
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

        $show->addImage($image);
        $em->persist($show);
        $em->flush();

        return $this->restful([new WrapperNormalizer()], new SuccessWrapper(null, "Image Uploaded"));
    }

    /**
     * @Route("/show/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="get_image_show")
     * @Method({"GET"})
     */
    public function getImageForShowAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token, $slug);

        if ($show == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        return $this->restful([
            new WrapperNormalizer(),
            new TagNormalizer(),
            new ImageNormalizer()], new SuccessWrapper($show->getImages()->toArray(), "Images"));

    }

    /**
     * @Route("/show/{token}/{slug}/header",
     *     options = { "expose" = true },
     *     name="post_header_image_show")
     * @Method({"POST"})
     */
    public function postImageShowHeader(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token, $slug);

        if ($show == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        $src = $request->files->get('image', null);
        if ($src == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Image Not Found"), 410);
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

        $show->setHeaderImage($image);
        $em->persist($show);
        $em->flush();

        return $this->restful([new WrapperNormalizer()], new SuccessWrapper(null, "Image Header Set"));
    }

    /**
     * @Route("/show/{token}/{slug}/header",
     *     options = { "expose" = true },
     *     name="delete_show_image_header")
     * @Method({"DELETE"})
     */
    public function deleteImageShowHeader(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token, $slug);

        if ($show == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Show Permission Error"), 400);
        }
        $em->remove($show->getHeaderImage());
        return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Header Image Deleted"), 400);

    }


}
