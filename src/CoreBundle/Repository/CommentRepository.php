<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Repository;


use CoreBundle\Entity\Comment;
use Doctrine\ORM\EntityRepository;

class CommentRepository  extends EntityRepository
{
    /**
     * @param Comment $comment
     */
    public function getChildComments($comment)
    {

    }
}