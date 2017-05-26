<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/25/17
 * Time: 4:11 PM
 */

namespace CoreBundle\Helper;


use CoreBundle\Entity\Comment;

class CommentWalker
{
    private $depth;

    private $comment;

    function __construct($depth)
    {
        $this->depth = $depth;
    }

    public  function setDepth($depth)
    {
        $this->depth = $depth;
    }

    public  function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param Comment $comment
     * @param $callback
     */
    public function walk($comment,$callback)
    {
    }
}