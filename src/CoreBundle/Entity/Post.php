<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Blog
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\PostRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"all"})
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
     * @var string
     *
     * @ORM\Column(name="token", type="string",length=20, nullable=false,unique=true)
     *
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string",length=100, nullable=false,unique=true)
     * @Assert\Regex("/^[a-zA-Z0-9\-]+$/")
     */
    private $slug;

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
     * @ORM\Column(name="excerpt", type="text", length=65535, nullable=true)
     */
    private $excerpt;

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
     * @var resource
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=false)
     */
    private $content = "";

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
     * @ORM\JoinTable(name="post_image",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     *      )
     * @return ArrayCollection
     */
    private $images;

    /**
     * @var ArrayCollection
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Comment",inversedBy="post")
     * @ORM\JoinTable(name="post_comment",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $comments;

    /**
     * @var ArrayCollection
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Category", indexBy="category")
     * @ORM\JoinTable(name="post_category",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    private $categories;

    /**
     * @var ArrayCollection
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Tag", indexBy="tag")
     * @ORM\JoinTable(name="post_tag",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
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
            $this->token = substr(bin2hex(random_bytes(12)),10);
            $this->createdAt = new \DateTime('now');
        }
    }

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection();


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

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
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

    /**
     * @return resource
     */
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

    public function setExcerpt($postExcerpt)
    {
        $this->excerpt = $postExcerpt;
    }

    public function getExcerpt()
    {
        return $this->excerpt;
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
        $this->tags->add($tag);
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function removeTag($tag)
    {
        return $this->tags->remove($tag);
    }


    /**
     * @return ArrayCollection
     */
    public  function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Category $category
     */
    public  function addCategory($category)
    {
        $this->categories->add($category);
    }

    /**
     * @param string $category
     * @return mixed
     */
    public function removeCategory($category)
    {
        return $this->categories->remove($category);
    }

    public  function getCategories()
    {
        return $this->categories;
    }


    public function getToken()
    {
        return $this->token;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $result = str_replace(' ','-',$slug);
        $result = preg_replace('/\-+/', '-',$result);
        $this->slug = $result;
    }

}

