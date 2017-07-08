<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Validation\Constraints As CoreAssert;
use JMS\Serializer\Annotation As JMS;


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
     * @JMS\Exclude
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string",length=100, nullable=false, unique=true)
     * @JMS\Groups({"detail","list"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @JMS\Groups({"detail","list"})
     * @ORM\Column(name="token", type="string",length=20, nullable=false,unique=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string",length=100, nullable=false,unique=true)
     * @Assert\Regex("/^[a-zA-Z0-9\-]+$/")
     * @Assert\NotBlank()
     * @JMS\Groups({"detail","list"})
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $updatedAt;


    /**
     * @var string
     * @ORM\Column(name="excerpt", type="text", length=6000, nullable=true)
     * @JMS\Groups({"detail","list"})
     * @Assert\NotBlank()
     */
    private $excerpt;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=40, nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $status;

    /**
     * @var boolean
     * @ORM\Column(name="is_pinned", type="boolean", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $isPinned = 0;

    /**
     * @var object
     * @CoreAssert\Delta
     * @Assert\NotBlank()
     * @ORM\Column(name="content",  type="text", nullable=false)
     * @JMS\Groups({"detail","list"})
     */
    private $content;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     * @JMS\Groups({"list","detail"})
     */
    private $author;


    /**
     * @var ArrayCollection
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Comment",inversedBy="post")
     * @ORM\JoinTable(name="post_comment",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id", unique=true)}
     *      )
     * @JMS\Exclude
     */
    private $comments;

    /**
     * @var ArrayCollection
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Category", cascade={"persist"})
     * @ORM\JoinTable(name="post_category",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     * @JMS\Groups({"detail","list"})
     */
    private $categories;

    /**
     * @var ArrayCollection
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"})
     * @ORM\JoinTable(name="post_tag",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     * @JMS\Groups({"detail","list"})
     */
    private $tags;


    /**
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Media")
     * @ORM\JoinTable(name="post_media",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", unique=true)}
     *      )
     * @return ArrayCollection
     */
    private $media;

    /**
     * @var ShowMeta
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="PostMeta",mappedBy="post")
     */
    private $postMeta;

    private $deltaRenderer = 'HTML';


    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->updatedAt = new \DateTime('now');

        if ($this->createdAt === null) {
            $this->token = substr(bin2hex(random_bytes(12)),10);
            $this->createdAt = new \DateTime('now');
        }
    }

    public function __construct()
    {
        $this->postMeta = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function setDeltaRenderer($praser)
    {
        $this->deltaRenderer = $praser;
    }

    public function getDeltaRenderer()
    {
        return $this->deltaRenderer;
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

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public  function getContent()
    {
        return $this->content;
    }

    public function getName()
    {
        return $this->name;
    }

    public  function setName($name)
    {
        $this->name = $name;
    }

    public function isPinned()
    {
        return (bool)$this->isPinned;
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
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function hasTag(Tag $tag)
    {
        return $this->tags->contains($tag);
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function removeTag($tag)
    {
        return $this->tags->removeElement($tag);
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
    public  function addCategory(Category $category)
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
    }

    public function hasCategory( Category $category) {
        return $this->categories->contains($category);
    }

    /**
     * @param string $category
     * @return mixed
     */
    public function removeCategory($category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * @return ArrayCollection
     */
    public  function getCategories()
    {
        return $this->categories->getValues();
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

    public function getPostMeta()
    {
        return $this->postMeta;
    }


    public function addMedia(Media $media)
    {
        if(!$this->media->contains($media))
        {
            $this->media->add($media);
        }
    }

    public function removeMedia(Media $media)
    {
        $this->media->removeElement($media);
    }

}

