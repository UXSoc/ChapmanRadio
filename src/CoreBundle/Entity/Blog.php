<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Blog
 *
 * @ORM\Table(name="blog")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\BlogRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Blog
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
     * @ORM\Column(name="name", type="string",length=100, nullable=false,unique=true)
     *
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


    /**
     * @var string
     *
     * @ORM\Column(name="post_excerpt", type="text", length=65535, nullable=true)
     */
    private $postExcerpt;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=40, nullable=true)
     */
    private $status;

    /**
     * @var boolean
     * @ORM\Column(name="is_pinned", type="boolean", nullable=true)
     */
    private $isPinned = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="blob", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;

    /**
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Image")
     * @ORM\JoinTable(name="blog_image",
     *      joinColumns={@ORM\JoinColumn(name="blog_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     *      )
     * @return ArrayCollection
     */
    private $images;

    /**
     * @var ArrayCollection
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Comment")
     * @ORM\JoinTable(name="blog_comment",
     *      joinColumns={@ORM\JoinColumn(name="blog_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $comments;

    /**
     * @var Category[]
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Category", indexBy="category")
     * @ORM\JoinTable(name="blog_category",
     *      joinColumns={@ORM\JoinColumn(name="blog_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    private $categories;

    /**
     * @var Tag[]
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Tag", indexBy="tag")
     * @ORM\JoinTable(name="blog_tag",
     *      joinColumns={@ORM\JoinColumn(name="blog_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    private $tags;


    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->updatedAt = new \DateTime('now');

        if ($this->createdAt == null) {
            $this->createdAt = new \DateTime('now');
        }
    }

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function addImage($image)
    {
        $this->images->add($image);
    }

    public  function removeImage($image)
    {
        $this->images->remove($image);
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getName()
    {
        return $this->name;
    }

    public  function setName($name)
    {
        $this->name = $name;
    }

    public  function getContent()
    {
        return $this->content;
    }

    public function getIsPinned()
    {
        return $this->isPinned;
    }

    public function setIsPinned($pinned)
    {
        $this->isPinned = $pinned;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setPostExcerpt($postExcerpt)
    {
        $this->postExcerpt = $postExcerpt;
    }

    public function getPostExcerpt()
    {
        return $this->postExcerpt;
    }

    public function addComment($comment)
    {
        $this->comments->add($comment);
    }

    /**
     * return array of comments
     * @return ArrayCollection
     */
    public function getComments()
    {
       return $this->comments;
    }

    public  function getCreatedAt()
    {
        return $this->createdAt;
    }
    public  function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param Tag $tag
     */
    public function addTag($tag)
    {
        $this->tags[$tag->getTag()] = $tag;
    }

    /**
     * @param Category $category
     */
    public  function addCategory($category)
    {
        $this->categories[$category->getCategory()] = $category;
    }

    public  function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return Tag[]
     */
    public  function getTags()
    {
        return $this->tags;
    }

}

