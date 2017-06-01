<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 *
 * @ORM\Table(name="comment", indexes={@ORM\Index(name="comment_comment_id_id_fk", columns={"comment_id"}), @ORM\Index(name="comment_user_id_fk", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\CommentRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Comment
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string",length=100, nullable=false,unique=true)
     *
     */
    private $token;

    /**
     * @var Comment
     *
     * @ORM\ManyToOne(targetEntity="Comment",inversedBy="childrenComment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     * })
     */
    private $parentComment = null;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parentComment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="comment_id")
     * })
     */
    private $childrenComment = null;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Post",mappedBy="comments")
     *
     */
    private $post;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Show",mappedBy="comments")
     *
     */
    private $show;

    function __construct()
    {
        $this->childrenComment = new ArrayCollection();
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->updatedAt = new \DateTime('now');
        if ($this->createdAt == null) {
            $this->token = substr(bin2hex(random_bytes(12)),10);
            $this->createdAt = new \DateTime('now');
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public  function getContent()
    {
        return $this->content;
    }

    public  function setContent($content)
    {
        $this->content = $content;
    }

    public function getParentComment()
    {
        return $this->parentComment;
    }

    public  function setParentComment($parentComment)
    {
        $this->parentComment = $parentComment;
    }

    public function getChildrenComments()
    {
        return $this->childrenComment;
    }

    public  function getUser()
    {
        return $this->user;
    }

    public  function setUser($user)
    {
        $this->user = $user;
    }

    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    public  function getCreateAt()
    {
        return $this->createdAt;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getBlog()
    {
        return $this->post->first();
    }

    public function getShow()
    {
        return $this->show->first();
    }
}

