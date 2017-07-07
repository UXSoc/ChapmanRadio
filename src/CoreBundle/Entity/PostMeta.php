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
class PostMeta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_key", type="string", length=20, nullable=true)
     */
    private $metaKey;

    /**
     * @var array
     *
     * @ORM\Column(name="meta_value", type="object", nullable=true)
     */
    private $metaValue;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="postMeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * })
     */
    private $post;

    public function getId()
    {
        return $this->id;
    }

    public function getMetaKey()
    {
        return $this->metaKey;
    }

    public function setMetaKey($key)
    {
        $this->metaKey = $key;
    }

    public function getMetaValue()
    {
        return $this->metaValue;
    }

    public function setMetaValue($value)
    {
        $this->metaValue = $value;
    }

    public function getPost()
    {
        return $this->post;
    }
}