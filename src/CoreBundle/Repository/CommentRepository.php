<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Repository;


use CoreBundle\Entity\Post;
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Show;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class CommentRepository  extends EntityRepository
{
    /**
     * @param Post $post
     * @return Comment[]
     */
    public function getAllRootCommentsForPost(Post $post)
    {
        return $this->createQueryBuilder('c')
            ->join('c.post','g',"ON")
            ->where('g.id = :id AND c.parentComment is NULL')
            ->setParameter('id',$post->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Show $show
     * @return Comment[]
     */
    public function getAllRootCommentsForShow(Show $show)
    {
        return $this->createQueryBuilder('c')
            ->join('c.show','g',"ON")
            ->where('g.id = :id AND c.parentComment is NULL')
            ->setParameter('id',$show->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Post $post
     * @param Comment $comment
     * @return boolean
     */
    public function isCommentAssignedToPost(Post $post,Comment $comment)
    {
       return count($this->createQueryBuilder('c')
            ->join('c.post','g',"ON")
            ->where('g.id = :pid AND c.id = :cid')
            ->setParameter('pid',$post->getId())
            ->setParameter('cid',$comment->getId())
            ->getQuery()
            ->getResult()) > 0;
    }

    /**
     * @param Show $show
     * @param Comment $comment
     * @return boolean
     */
    public function isCommentAssignedToShow(Show $show,Comment $comment)
    {
        return count($this->createQueryBuilder('c')
            ->join('c.show','g',"ON")
            ->where('g.id = :sid AND c.id = :cid')
            ->setParameter('sid',$show->getId())
            ->setParameter('cid',$comment->getId())
            ->getQuery()
            ->getResult()) > 0;

    }

    /**
     * @param Show $show
     * @param string $token
     * @return Comment | null
     *
     */
    public function getCommentByShowAndToken(Show $show,  $token)
    {
        return $this->findOneBy(['id' => $show->getId(), 'token' => $token]);
    }


    /**
     * @param Post $post
     * @param string $token
     * @return Comment | null
     */
    public function getCommentByPostAndToken($post, $token)
    {
        return $this->findOneBy(['id' => $post->getId(), 'token' => $token]);
    }


    /**
     * @param string $token
     * @return Comment | null
     */
    public function getCommentByToken($token)
    {
        return $this->findOneBy(['token' => $token]);

    }
}
