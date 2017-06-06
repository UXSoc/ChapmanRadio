<?php

namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Entity\Image;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\Tag;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\ImageNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\ShowRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\ShowVoter;
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
class ShowController extends Controller
{
    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/show",
     *     options = { "expose" = true },
     *     name="post_show")
     * @Method({"POST"})
     */
    public function putShowAction(Request $request)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        $show = new Show();
        $show->setName($request->get('name'));
        $show->setSlug($request->get('slug', $request->get('name')));
        $show->setDescription($request->get('description'));
        $show->setEnableComments($request->get('enable_comments'));

        $errors = $validator->validate($show);
        if($errors->count() > 0)
            return RestfulEnvelope::errorResponseTemplate('Invalid show')->addErrors($errors)->response();

        $em->persist($show);
        $em->flush();

        return RestfulEnvelope::successResponseTemplate('Show created',$show,[new ShowNormalizer()]);
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
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get(Show::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);

            $show->setName($request->get('name', $show->getName()));
            $show->setSlug($request->get('slug', $show->getSlug()));
            $show->setDescription($request->get('description', $show->getDescription()));
            $show->setEnableComments($request->get('enable_comments', $show->getEnableComments()));

            $errors = $validator->validate($show);
            if($errors->count() > 0)
                return RestfulEnvelope::errorResponseTemplate('Invalid show')->addErrors($errors)->response();

            $em->persist($show);
            $em->flush();

            return RestfulEnvelope::successResponseTemplate('Show updated',$show,[new ShowNormalizer()]);
        }
        return RestfulEnvelope::errorResponseTemplate('Invalid show')->response();
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
        if ( $show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(ShowVoter::DELETE, $show);
            $em->remove($show);
            $em->flush();
        }
        return RestfulEnvelope::errorResponseTemplate('Show not found')->setStatus(410)->response();
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
        if($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
            if($show->getTags()->containsKey($tag))
                return RestfulEnvelope::errorResponseTemplate('duplicate Tag')->response();

            /** @var TagRepository $tagRepository */
            $tagRepository = $this->get(Tag::class);

            $tag = $tagRepository->getOrCreateTag($tag);
            $em->persist($tag);
            $show->addTag($tag);
            $em->persist($show);
            $em->flush();

            return RestfulEnvelope::successResponseTemplate('Tag added',$tag,[new TagNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Post not found')->response();
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
        if($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);

            if($s = $show->removeTag($tag)) {
                $em->persist($show);
                $em->flush();
                return RestfulEnvelope::successResponseTemplate('Tag deleted', $s,
                    [new TagNormalizer()])->response();
            }
            return RestfulEnvelope::errorResponseTemplate('Tag not found')->setStatus(410)->response();

        }
        return RestfulEnvelope::errorResponseTemplate('Show not found')->response();

    }

    /**
     * @Route("/show/{token}/{slug}/image",
     *     options = { "expose" = true },
     *     name="put_image_show")
     * @Method({"PUT"})
     */
    public function putImageForShowAction(Request $request, $token, $slug)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);
        /** @var Show $show */
       if($show = $showRepository->getShowByTokenAndSlug($token, $slug))
       {
           $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);

           $src = $request->files->get('image', null);
           $image = new Image();
           $image->setImage($src);
           $image->setAuthor($this->getUser());

           $errors = $validator->validate($image);
           if($errors->count() > 0)
               return RestfulEnvelope::errorResponseTemplate('invalid Image')->addErrors($errors)->response();

           $imageService->saveImageToFilesystem($image);
           $em->persist($image);

           $show->addImage($image);
           $em->persist($show);
           $em->flush();
           return RestfulEnvelope::successResponseTemplate('Image Uploaded',$image,[new ImageNormalizer()])->response();
       }
        return RestfulEnvelope::errorResponseTemplate('Image error')->response();
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

        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
            return RestfulEnvelope::successResponseTemplate('Image Header Uploaded',$show->getImages()->toArray(),[new ImageNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Show not found')->setStatus(410)->response();

    }

    /**
     * @Route("/show/{token}/{slug}/header",
     *     options = { "expose" = true },
     *     name="post_header_image_show")
     * @Method({"POST"})
     */
    public function postImageShowHeaderAction(Request $request, $token, $slug)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        /** @var ImageUploadService $imageService */
        $imageService = $this->get(ImageUploadService::class);

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        if ( $show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);

            $image  = $imageService->createImage($request->files->get('image', null),$this->getUser());
            $errors = $validator->validate($image);
            if($errors->count() > 0)
                return RestfulEnvelope::errorResponseTemplate('invalid Image')->addErrors($errors)->response();

            $imageService->saveImageToFilesystem($image);
            $em->persist($image);

            $show->setHeaderImage($image);
            $em->persist($show);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Image Header Uploaded',$image,[new ImageNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Show not found')->setStatus(410)->response();
    }

    /**
     * @Route("/show/{token}/{slug}/header",
     *     options = { "expose" = true },
     *     name="delete_show_image_header")
     * @Method({"DELETE"})
     */
    public function deleteImageShowHeaderAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug)) {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
            $em->remove($show->getHeaderImage());
            return RestfulEnvelope::successResponseTemplate('Image Header deleted')->response();
        }
        return RestfulEnvelope::errorResponseTemplate('Show not found')->setStatus(410)->response();

    }


}
