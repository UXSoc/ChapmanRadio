<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/24/17
 * Time: 10:46 PM
 */

namespace RestfulBundle\Controller\Api\V3;


use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Post;
use CoreBundle\Entity\Comment;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\BlogNormalizer;
use CoreBundle\Normalizer\CategoryNormalizer;
use CoreBundle\Normalizer\CommentNormalizer;
use CoreBundle\Normalizer\PaginatorNormalizer;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\CategoryRepository;
use CoreBundle\Repository\PostRepository;
use CoreBundle\Repository\CommentRepository;
use CoreBundle\Repository\TagRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/api/v3/")
 */
class BlogController extends BaseController
{

    /**
     * @Route("tags/{name}",
     *     options = { "expose" = true },
     *     name="get_tags")
     * @Method({"GET"})
     */
    public function getTags(Request $request,$name)
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->get('core.tag_repository');

        $tags = $tagRepository->findTag($name,20);
        return $this->restful([new WrapperNormalizer(),
            new TagNormalizer()],new SuccessWrapper($tags,null));
    }

    /**
     * @Route("categories/{name}",
     *     options = { "expose" = true },
     *     name="get_categories")
     * @Method({"GET"})
     */
    public function getCategories(Request $request,$name)
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->get('core.category_repository');

        $categories = $categoryRepository->findCategory($name,20);
        return $this->restful([
            new WrapperNormalizer(),
            new CategoryNormalizer()],new SuccessWrapper($categories,null));
    }

    /**
     * @Route("post",
     *     options = { "expose" = true },
     *     name="get_posts")
     * @Method({"GET"})
     */
    public  function  getPostsAction(Request $request)
    {
        /** @var PostRepository $postRepository */
        $postRepository = $this->get('core.post_repository');

        $q = $postRepository->createQueryBuilder('p');

        $name = $request->get('name',null);
        if($name)
            $q->where($q->expr()->like('p.name',':name'))
              ->setParameter('name','%'.$request->get('name').'%');

        $pagination = new Paginator($q->getQuery());
        $perPage = $request->get("entries",10) > 20 ? 20 :  $request->get("entries",20);
        $pagination->getQuery()->setMaxResults($perPage);
        $pagination->getQuery()->setFirstResult($perPage * $request->get("page",0));


        $s = new SuccessWrapper();
        $s->setPayload($pagination);
        return $this->restful([new BlogNormalizer(),
            new UserNormalizer(),
            new PaginatorNormalizer(),
            new WrapperNormalizer()],$s,200);

    }

    /**
     * @Route("post/{token}/{slug}/tags",
     *     options = { "expose" = true },
     *     name="get_post_tags")
     * @Method({"GET"})
     */
    public function getPostTags(Request $request, $token, $slug)
    {
        /** @var PostRepository $postRepository */
        $postRepository = $this->get('core.post_repository');

        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token,$slug);

        if($post == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        return $this->restful([
            new TagNormalizer(),
            new WrapperNormalizer()],new SuccessWrapper($post->getTags()->getValues()));
    }

    /**
     * @Route("post/{token}/{slug}/categories",
     *     options = { "expose" = true },
     *     name="get_post_categories")
     * @Method({"GET"})
     */
    public function getPostCategories(Request $request, $token, $slug)
    {
        /** @var PostRepository $postRepository */
        $postRepository = $this->get('core.post_repository');

        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token,$slug);

        if($post == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        return $this->restful([
            new CategoryNormalizer(),
            new WrapperNormalizer()],new SuccessWrapper($post->getCategories()->getValues()));
    }

    /**
     * @Route("post/{token}/{slug}",
     *     options = { "expose" = true },
     *     name="get_post", )
     * @Method({"GET"})
     */
    public  function  gePostAction(Request $request, $token, $slug)
    {
        /** @var PostRepository $blogRepository */
        $blogRepository = $this->get('core.post_repository');
        /** @var Post $post */
        $post = $blogRepository->findOneBy(['token' => $token,'slug' => $slug]);

        if($post == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        return $this->restful([
            new BlogNormalizer(),
            new UserNormalizer(),
            new PaginatorNormalizer(),
            new WrapperNormalizer()],new SuccessWrapper($post));

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("post/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="post_post_comment")
     * @Method({"POST"})
     */
    public function postPostCommentAction(Request $request,$token,$slug,$comment_token = null){
        /** @var PostRepository $postRepository */
        $postRepository = $this->get('core.post_repository');

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.comment_repository');

        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token,$slug);

        if($post == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        $comment = new Comment();
        $comment->setContent($request->get("content"));
        $comment->setUser($this->getUser());

        if($comment_token != null) {
            try {
                $comment->setParentComment($commentRepository->getCommentByPostAndToken($post, $comment_token));
            } catch (NoResultException $e) {
                return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Unknown Comment"), 410);
            }
        }

        $errors = $this->validateEntity($comment);
        if($errors->count() > 0)
        {
            $error = new ErrorWrapper("invalid token");
            $error->addErrors($this->validateEntity($comment));
            $error->setMessage("Invalid Comment");
            return $this->restful([new WrapperNormalizer()],$error,400);
        }

        return $this->restful([
            new WrapperNormalizer(),
            new CommentNormalizer(),
            new UserNormalizer()
        ],new SuccessWrapper($comment,"Comment Added"));

    }


    /**
     * @Route("post/{token}/{slug}/comment/{comment_token}",
     *     options = { "expose" = true },
     *     name="get_blog_comment")
     * @Method({"GET"})
     */
    public function getPostCommentAction(Request $request,$token,$slug,$comment_token = null){
        /** @var PostRepository $postRepository */
        $postRepository = $this->get('core.post_repository');

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->get('core.comment_repository');

        /** @var Post $post */
        $post = $postRepository->getPostByTokenAndSlug($token,$slug);

        if($post == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Blog Post Not Found"),410);

        if($comment_token == null) {
            return $this->restful([new CommentNormalizer(),
                new UserNormalizer(),
                new WrapperNormalizer()],
                new SuccessWrapper($commentRepository->getAllRootCommentsForPost($post)));
        }
        else
        {
            try {
                $comment = $commentRepository->getCommentByPostAndToken($post, $comment_token);
                return $this->restful([new CommentNormalizer(),
                    new UserNormalizer(),
                    new WrapperNormalizer()],
                    new SuccessWrapper($comment));

            } catch (NoResultException $e) {
                return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Unknown Comment"), 410);
            }

        }

    }




}