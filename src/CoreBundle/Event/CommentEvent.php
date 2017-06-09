<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/8/17
 * Time: 12:00 AM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Comment;

class CommentEvent
{
    private $comment;

    function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }
}