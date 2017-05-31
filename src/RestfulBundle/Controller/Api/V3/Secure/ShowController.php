<?php
namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Show;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\GenreRepository;
use CoreBundle\Repository\ShowRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\ShowVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


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
        $show->setSlug($request->get('slug',$request->get('name')));
        $show->setDescription($request->get('description'));
        $show->setEnableComments($request->get('enable_comments'));


        $errors = $this->validateEntity($show);
        if($errors->count() > 0) {
            $error = new ErrorWrapper();
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()], $error, 400);
        }

        $em->persist($show);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new ShowNormalizer()
        ],new SuccessWrapper($show,"Show Created"));
    }



    /**
     * @Security("has_role('ROLE_DJ')")
     * @Route("show/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="patch_show")
     * @Method({"PATCH"})
     */
    public function patchShowAction(Request $request,$token,$slug){

        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token,$slug);
        if ($show == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Show Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Show Permission Error"), 400);
        }

        $show->setName($request->get('name',$show->getName()));
        $show->setSlug($request->get('slug',$show->getSlug()));
        $show->setDescription($request->get('description',$show->getDescription()));
        $show->setEnableComments($request->get('enable_comments',$show->getEnableComments()));

        $errors = $this->validateEntity($show);
        if($errors->count() > 0) {
            $error = new ErrorWrapper();
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()], $error, 400);
        }

        $em->persist($show);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new ShowNormalizer()
        ],new SuccessWrapper($show,"Show Updated"));


    }


    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("show/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="delete_show")
     * @Method({"DELETE"})
     */
    public function deleteShowAction(Request $request,$token,$slug){
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');

        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token,$slug);
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
     * @Route("/post/{token}/{slug}/tag/{tag}", options = { "expose" = true }, name="put_tag_post")
     * @Method({"PUT"})
     */
    public function putTagForShowAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get('core.show_repository');
        /** @var Show $show */
        $show = $showRepository->getPostByTokenAndSlug($token,$slug);

        if ($show == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Show Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }
        if ($show->getTags()->containsKey($tag))
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Duplicate Tag Found"), 400);

        /** @var TagRepository $tagRepository */
        $tagRepository = $this->get("core.tag_repository");
        $tag = $tagRepository->getOrCreateTag($tag);
        $em->persist($tag);
        $em->flush();
        $show->addTag($tag);

        $em->persist($show);
        $em->flush();

        return $this->restful([new WrapperNormalizer()], new SuccessWrapper(null, "Tag added"));

    }

    /**
     * @Route("/post/{token}/{slug}/tag/{tag}", options = { "expose" = true }, name="delete_tag_post")
     * @Method({"DELETE"})
     */
    public function deleteTagForPostAction(Request $request, $token, $slug, $tag)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $postRepository */
        $postRepository = $this->get('core.post_repository');
        /** @var Show $show */
        $show = $postRepository->getPostByTokenAndSlug($token,$slug);

        if ($show == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Blog Post Not Found"), 410);

        try {
            $this->denyAccessUnlessGranted(ShowVoter::EDIT, $show);
        } catch (\Exception $exception) {
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Permission Error"), 400);
        }

        $result = $show->removeTag($tag);
        if($result == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Post Does Not Have Tag"), 410);

        $em->persist($show);
        $em->flush();

        return $this->restful([
            new WrapperNormalizer(),
            new TagNormalizer()],new SuccessWrapper($result,"Tag Deleted"));

    }


}