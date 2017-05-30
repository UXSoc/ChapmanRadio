<?php
namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Post;
use CoreBundle\Entity\Tag;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\BlogNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Repository\TagRepository;
use CoreBundle\Security\PostVoter;
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
     * @Route("post", options = { "expose" = true }, name="post_post")
     * @Method({"POST"})
     */
    public function postPostAction() {

    }



    /**
     * @Route("post/{token}/{slug}", options = { "expose" = true }, name="patch_post")
     * @Method({"PATCH"})
     */
    public function patchPostAction(Request $request,$token,$slug){

        /** @var PostRepository $blogRepository */
        $blogRepository = $this->get('core.post_repository');
        /** @var Post $post */
        $post = $blogRepository->findOneBy(['token' => $token,'slug' => $slug]);

        if($post == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        }
        catch (\Exception $exception)
        {
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Post Permission Error"),400);
        }

        $post->setContent($request->get("content",$post->getContent()));
        $post->setName($request->get("name",$post->getName()));
        $post->setSlug($request->get("slug",$post->getSlug()));
        $post->setExcerpt($request->get("excerpt",$post->getExcerpt()));
        $post->setIsPinned($request->get("pinned",$post->getIsPinned()));

        $errors = $this->validateEntity($post);
        if($errors->count() > 0)
        {
            $error = new ErrorWrapper(null);
            $error->addErrors($errors);
            return $this->restful([new WrapperNormalizer()],$error,400);
        }

        return $this->restful([
            new BlogNormalizer(),
            new UserNormalizer(),
            new PaginatorNormalizer(),
            new WrapperNormalizer()],new SuccessWrapper($post));

    }


    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("post/{token}/{slug}", options = { "expose" = true }, name="delete_post")
     * @Method({"DELETE"})
     */
    public function deletePostAction(Request $request,$token,$slug){
        /** @var PostRepository $postRepository */
        $postRepository = $this->get('core.post_repository');
        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token,$slug);

        if($post == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);
        }
        catch (\Exception $exception)
        {
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Post Permission Error"),400);
        }
        return $this->restful([new WrapperNormalizer()],new SuccessWrapper(null,"Blog Post Deleted"));

    }
    //----------------------------------------------------------------------------------------

    /**
     * @Route("post/{token}/{slug}/tag/{tag}", options = { "expose" = true }, name="put_tag_post")
     * @Method({"PUT"})
     */
    public function putTagForPostAction(Request $request,$token,$slug,$tag) {
        /** @var PostRepository $blogRepository */
        $blogRepository = $this->get('core.post_repository');
        /** @var Post $post */
        $post = $blogRepository->findOneBy(['token' => $token,'slug' => $slug]);

        if($post == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        try {
            $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        }
        catch (\Exception $exception)
        {
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Post Permission Error"),400);
        }
        if($post->getTags()->containsKey($tag))
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Duplicate Tag Found"),400);

        /** @var TagRepository $tagRepository */
        $tagRepository = $this->get("core.tag_repository");
        $tag = $tagRepository->findOrCreateTag($tag);
        $post->addTag($tag);

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return $this->restful([new WrapperNormalizer()],new SuccessWrapper(null,"Tag added"));


    }

    /**
     * @Route("blog/post/{post}/tag/{tag}", options = { "expose" = true }, name="delete_tag_post")
     * @Method({"DELETE"})
     */
    public function deleteTagForPostAction() {

    }


    /**
     * @Route("blog/post/{post}/category/{category}", options = { "expose" = true }, name="put_category_post")
     * @Method({"PUT"})
     */
    public function putCategoryForPostAction() {

    }

    /**
     * @Route("blog/post/{post}/category/{category}", options = { "expose" = true }, name="delete_category_post")
     * @Method({"DELETE"})
     */
    public function deleteCategoryForPostAction() {

    }


    //----------------------------------------------------------------------------------------

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/tag", options = { "expose" = true }, name="put_tag")
     * @Method({"PUT"})
     */
    public function putTagAction() {

    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/tag", options = { "expose" = true }, name="delete_tag")
     * @Method({"DELETE"})
     */
    public function deleteTagAction(){

    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/tag", options = { "expose" = true }, name="put_category")
     * @Method({"PUT"})
     */
    public function putCategoryAction() {

    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("blog/tag", options = { "expose" = true }, name="delete_category")
     * @Method({"DELETE"})
     */
    public function deleteCategoryAction(){

    }


}