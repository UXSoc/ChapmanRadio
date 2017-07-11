<?php
namespace RestfulBundle\Controller\Api\V3\Dashboard;


use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Media;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\Tag;
use CoreBundle\Event\MediaSaveEvent;
use CoreBundle\Form\CommentType;
use CoreBundle\Form\MediaType;
use CoreBundle\Form\ShowType;
use CoreBundle\Repository\MediaRepository;
use CoreBundle\Repository\ShowRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\ShowVoter;
use FOS\RestBundle\Controller\FOSRestController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/")
 */
class ShowController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Rest\Post("show/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="post_show_comment")
     */
    public function postShowCommentAction(Request $request, $token, $slug, $comment_token = null)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token,$slug))
        {
            $comment = new Comment();
            $comment->setUser($this->getUser());
            $show->addComment($comment);

            $form = $this->createForm(CommentType::class,$comment);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($comment);
                $em->persist($show);
                $em->flush();
                return $this->view(['comment' => $comment]);
            }
            return $this->view($form);

        }
        throw $this->createNotFoundException('Show not Found');
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Get("show/datatable",
     *     options = { "expose" = true },
     *     name="get_show_dataTable")
     */
    public function getPostDatatableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        return $this->view(["datatable" => $showRepository->dataTableFilter($request)]);
    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Rest\Post("show",
     *     options = { "expose" = true },
     *     name="post_show")
     * @Rest\View(serializerGroups={"detail"})
     */
    public function postShowAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $show = new Show();
        $form = $this->createForm(ShowType::class,$show);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($show);
            $em->flush();
            $this->view(['show' => $show]);
        }
        $this->view($form);
    }


    /**
     * @Rest\Post("show/{token}/{slug}/media",
     *     options = { "expose" = true },
     *     name="post_media_show")
     */
    public function postShowMediaAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);

            $media = new Media();
            $media->setAuthor($this->getUser());
            $form = $this->createForm(MediaType::class,$media);
            if($form->isSubmitted() && $form->isValid()) {
                /** @var EventDispatcher $dispatcher */
                $dispatcher = $this->get('event_dispatcher');

                $event = new MediaSaveEvent($media);
                $dispatcher->dispatch(MediaSaveEvent::NAME,$event);

                $em->persist($media);
                $show->addMedia($media);
                $em->persist($show);
                $em->flush();
                return $this->view(['media' => $media]);
            }
            return $this->view($form);
        }
        throw  $this->createNotFoundException('Media Not Found');

    }


    /**
     * @Security("has_role('ROLE_DJ')")
     * @Rest\Patch("show/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="patch_show")
     * @Rest\View(serializerGroups={"detail"})
     */
    public function patchShowAction(Request $request, $token, $slug)
    {

        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $em->getRepository(Show::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);

            $form = $this->createForm(ShowType::class,$show,[
                'method' => 'patch'
            ]);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($show);
                $em->flush();
                $this->view(['show' => $show]);
            }
            return $this->view($form);
        }
        throw $this->createNotFoundException('Show not Found');
    }


    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Rest\Delete("show/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="delete_show")
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
            return $this->view();
        }
        throw $this->createNotFoundException('Show not Found');
    }


    /**
     * @Rest\Put("show/{token}/{slug}/tag/{tag}",
     *      options = { "expose" = true },
     *     name="put_tag_show")
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
                throw $this->createNotFoundException('Show Not Found');

            /** @var TagRepository $tagRepository */
            $tagRepository = $this->get(Tag::class);

            $tag = $tagRepository->getOrCreateTag($tag);
            $em->persist($tag);
            $show->addTag($tag);
            $em->persist($show);
            $em->flush();

            return $this->view(['tag' => $tag->getTag()]);
        }
        throw $this->createNotFoundException('Show Not Found');
    }

    /**
     * @Rest\Delete("show/{token}/{slug}/tag/{tag}",
     *     options = { "expose" = true },
     *     name="delete_tag_show")
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
                return $this->view(['tag' => $s->getTag()]);
            }
            throw $this->createNotFoundException('Tag Not Found');
        }
        throw $this->createNotFoundException('Show Not Found');
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
            return $this->view();
        }
        throw $this->createNotFoundException('Show not Found');
    }
}