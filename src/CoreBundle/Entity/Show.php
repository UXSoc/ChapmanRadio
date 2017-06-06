<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;


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
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
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
     * @@Assert\Regex("^[a-zA-Z0-9\-]+$/")
     */
    private $slug;

    /**
     * @var resource
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private $score = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="profanity", type="boolean", nullable=false)
     */
    private $profanity = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="attendance_optional", type="boolean", nullable=false)
     */
    private $attendanceOptional = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


    /**
     * @var integer
     *
     * @ORM\Column(name="strike_count", type="integer", nullable=false)
     */
    private $strikeCount = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="suspended", type="boolean", nullable=true)
     */
    private $suspended = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_comments", type="boolean", nullable=true)
     */
    private $enableComments = false;

    /**
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Image")
     * @ORM\JoinTable(name="show_image",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $images;


    /**
    * Many Shows have Many Images.
    * @ORM\ManyToMany(targetEntity="Comment", inversedBy="show")
    * @ORM\JoinTable(name="show_comment",
    *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id", unique=true)}
    *      )
    * @return ArrayCollection
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ShowSchedule", mappedBy="show")
     *
     */
    private $showSchedule;

    /**
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Genre", indexBy="genre")
     * @ORM\JoinTable(name="show_genre",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     *      )
     * @var PersistentCollection
     */
    private $genres;


    /**
     * @var ArrayCollection
     *
     * Many Shows have Many Images.
     * @ORM\ManyToMany(targetEntity="Tag", indexBy="tag")
     * @ORM\JoinTable(name="show_tag",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id",onDelete="CASCADE")}
     * )
     */
    private $tags;



    /**
     * @ORM\ManyToMany(targetEntity="Dj", mappedBy="shows")
     * @ORM\JoinTable(name="show_comment",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="dj_id", referencedColumnName="id", unique=true)}
     *      )
     * @return ArrayCollection
     */
    private $djs;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Event", mappedBy="show")
     *
     */
    private $events;


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->showSchedule = new ArrayCollection();
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

    public function addImage($image)
    {
        $this->images->add($image);
    }

    public function removeImage($image)
    {
        $this->images->remove($image);
    }

    public function addShowSchedule($showSchedule)
    {
        $this->showSchedule = $showSchedule;
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
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Genre $genre
     */
    public function addGenre($genre)
    {
        $this->genres->set($genre->getGenre(),$genre);
    }

    /**
     * @param string $key
     */
    public  function removeGenre($key)
    {
        $this->genres->remove($key);
    }

    /**
     * @param Tag $tag
     */
    public function addTag($tag)
    {
        $this->tags->set($tag->getTag(),$tag);
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function removeTag($tag)
    {
        return $this->tags->remove($tag);
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

    public function setAttendenceOptional($optional)
    {
        $this->attendanceoptional = $optional;
    }

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
    public function updatedAt()
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

    public function addEvent($event)
    {
        $this->events->add($event);
    }


}

