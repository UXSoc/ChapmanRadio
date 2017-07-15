<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;

use CoreBundle\Validation\Constraints As CoreAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;


/**
 * Show
 *
 * @ORM\Table(name="shows")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ShowRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Show
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
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     * @JMS\Groups({"detail","list"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string",length=20, nullable=false,unique=true)
     * @JMS\Groups({"detail","list"})
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string",length=100, nullable=false,unique=true)
     * @@Assert\Regex("^[a-zA-Z0-9\-]+$/")
     * @JMS\Groups({"detail","list"})
     * @Assert\NotBlank()
     */
    private $slug;

    /**
     * @var resource
     *
     * @CoreAssert\Delta
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="text", nullable=false)
     * @JMS\Groups({"detail"})
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @JMS\Groups({"detail","list"})
     */
    private $createdAt;

    /**
     * @var integer
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private $score = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="profanity", type="boolean", nullable=false)
     * @JMS\Groups({"detail","list"})
     */
    private $profanity = false;

    /**
     * @var boolean
     * @ORM\Column(name="attendance_optional", type="boolean", nullable=false)
     */
    private $attendanceOptional = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $updatedAt;


    /**
     * @var integer
     * @ORM\Column(name="strike_count", type="integer", nullable=false)
     */
    private $strikeCount = 0;

    /**
     * @var boolean
     * @ORM\Column(name="suspended", type="boolean", nullable=true)
     */
    private $suspended = false;

    /**
     * @var boolean
     * @ORM\Column(name="archive", type="boolean", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $archive = false;

    /**
     * @var boolean
     * @ORM\Column(name="enable_comments", type="boolean", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $enableComments = false;


    /**
    * Many Shows have Many Images.
    * @ORM\ManyToMany(targetEntity="Comment", inversedBy="show")
    * @ORM\JoinTable(name="show_comment",
    *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id", unique=true)}
    *      )
    * @return ArrayCollection
    * @JMS\Exclude
    */
    private $comments;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="header_image_id", referencedColumnName="id")
     * })
     */
    private $headerImage;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="excerpt", type="text", length=6000, nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $excerpt;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="show")
     *
     */
    private $schedule;

    /**
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Genre", cascade={"persist"})
     * @ORM\JoinTable(name="show_genre",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     *      )
     * @var PersistentCollection
     * @JMS\Groups({"detail","list"})
     */
    private $genres;


    /**
     * @var ArrayCollection
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"})
     * @ORM\JoinTable(name="show_tag",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id",onDelete="CASCADE")}
     * )
     * @JMS\Groups({"detail","list"})
     */
    private $tags;



    /**
     * @ORM\ManyToMany(targetEntity="Dj", mappedBy="shows")
     * @ORM\JoinTable(name="show_comment",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="dj_id", referencedColumnName="id", unique=true)}
     *      )
     * @return ArrayCollection
     * @JMS\Groups({"detail","list"})
     */
    private $djs;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Event", mappedBy="show")
     *
     */
    private $events;

    /**
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Media", inversedBy="show")
     * @ORM\JoinTable(name="show_media",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", unique=true)}
     *      )
     * @return ArrayCollection
     */
    private $media;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="ShowMeta",mappedBy="show")
     */
    private $meta;


    private $deltaRenderer = 'HTML';


    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->meta = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->schedule = new ArrayCollection();
        $this->djs = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->tags = new ArrayCollection();

    }

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

    public function setDeltaRenderer($praser)
    {
        $this->deltaRenderer = $praser;
    }

    public function getDeltaRenderer()
    {
        return $this->deltaRenderer;
    }

    public function getExcerpt()
    {
        return $this->excerpt;
    }

    public  function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
    }

    public function addSchedule(Schedule $schedule)
    {
        $schedule->setShow($this);
        $this->schedule->add($schedule);
    }

    public function addDj($dj)
    {
        $this->djs->add($dj);
    }

    public function getDjs()
    {
        return $this->djs;
    }

    public function setScore($score)
    {
        $this->score = $score;
    }

    public  function getScore()
    {
        return $this->score;
    }

    public function getId()
    {
        return $this->id;
    }


    /**
     * @return ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }


    /**
     * @param Tag $tag
     */
    public function addGenre($tag)
    {
        if (!$this->genres->contains($tag)) {
            $this->genres->add($tag);
        }
    }

    /**
     * @param string $key
     */
    public  function removeGenre($genre)
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->remove($genre);
        }
    }


    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function removeTag($tag)
    {
        if (!$this->tags->contains($tag)) {
            return $this->tags->remove($tag);
        }
    }

    /**
     * @param Tag $tag
     */
    public function addTag($tag)
    {
        $this->tags->set($tag->getTag(),$tag);
    }



    public function getHeaderImage()
    {
        return $this->headerImage;
    }

    public function setHeaderImage($image)
    {
        $this->headerImage = $image;
    }

    public function getEnableComments()
    {
        return $this->enableComments;
    }

    public function setEnableComments($enableComments)
    {
        $this->enableComments = $enableComments;
    }

    public function getSuspended()
    {
        return $this->suspended;
    }

    public function setSuspended($suspend)
    {
        $this->suspended = $suspend;
    }

    public function getStrikeCount()
    {
        return $this->strikeCount;
    }

    public function setStrikeCount($count)
    {
        $this->strikeCount = $count;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getProfanity()
    {
        return $this->profanity;
    }

    public function setProfanity($profanity)
    {
        $this->profanity = $profanity;
    }

    /**
     * @param $optional
     */
    public function setAttendenceOptional($optional)
    {
        $this->attendanceoptional = $optional;
    }

    /**
     * @return bool
     */
    public function getAttendenceOptional()
    {
        return $this->attendanceOptional;
    }

    /**
     * get the Datetime of the show createdat
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * get the the time that the show was updated at
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Returns the description of the show
     * @return resource
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * set the show description
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment)
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

    public function getEvents()
    {
        return $this->events;
    }

    public function addEvent(Event $event)
    {
        $this->events->add($event);
    }

    public function setArchive($archive)
    {
        $this->archive = $archive;
    }

    public function getArchive()
    {
        return $this->archive;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function getMetaByKey($key,$create = false)
    {
        $collection = $this->meta->matching(Criteria::create()->where(Criteria::expr()->eq("key", $key)));
        if ($collection->isEmpty()) {
            if ($create === true) {
                $meta = new ShowMeta();
                $meta->setKey($key);
                $meta->setValue([]);
                $meta->setShow($this);
                $this->meta->add($meta);
                return $meta;
            }
            return null;
        }
        return $collection->first();
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

