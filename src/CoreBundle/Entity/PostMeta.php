<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/6/17
 * Time: 8:54 PM
 */

namespace CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Entity()
 * @ORM\Table(name="post_meta")
 *
 */
class PostMeta extends BaseMeta
{
    const FEATURE = "feature";

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="meta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * })
     */
    private $post;



    public function getPost()
    {
        return $this->post;
    }

    public function setPost($post)
    {
        $this->post = $post;
    }
}