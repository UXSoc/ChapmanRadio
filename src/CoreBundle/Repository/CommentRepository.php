<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Repository;


use CoreBundle\Entity\Blog;
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Show;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class CommentRepository  extends EntityRepository
{
    /**
     * @param Blog $post
     */
    public function getAllRootCommentsForBlogEntiry($post)
    {
        return $this->createQueryBuilder('c')
            ->join('c.posts','g',"ON")
            ->where('g.id = :id AND c.parentComment is NULL')
            ->setParameter('id',$post->getId())
            ->getQuery()
            ->getResult();
    }


    /**
     * @param Blog $post
     * @param Comment $comment
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function postHasComment($post,$comment)
    {
        return $this->createQueryBuilder('c')
            ->join('c.posts','g',"ON")
            ->where('g.id = :pid AND c.id = :cid')
            ->setParameter('pid',$post->getId())
            ->setParameter('cid',$comment->getId())
            ->getQuery()
            ->getSingleResult();

    }

    /**
     * @param Show $show
     * @param Comment $root
     */
    public function getAllRootCommentsForShowEntiry($show)
    {
        return $this->createQueryBuilder('c')
            ->join('c.shows','g',"ON")
            ->where('g.id = :id AND c.parentComment is NULL')
            ->setParameter('id',$show->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Show $show
     * @param Comment $comment
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function showHasComment($show,$comment)
    {
        return $this->createQueryBuilder('c')
            ->join('c.shows','g',"ON")
            ->where('g.id = :sid AND c.id = :cid')
            ->setParameter('sid',$show->getId())
            ->setParameter('cid',$comment->getId())
            ->getQuery()
            ->getSingleResult();

    }

    /**
     * @param $token
     * @return array
     */
    public function findCommentByToken($token)
    {
        return $this->createQueryBuilder('c')
            ->where('c.token = :token')
            ->setParameter('token',$token)
            ->getQuery()
            ->getResult();
    }
}