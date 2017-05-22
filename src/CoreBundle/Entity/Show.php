<?php

namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="description", type="blob", length=65535, nullable=false)
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
    private $score;

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
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=80, nullable=true)
     */
    private $genre;


    /**
     * @var integer
     *
     * @ORM\Column(name="strike_count", type="integer", nullable=true)
     */
    private $strikeCount;

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
    * @ORM\ManyToMany(targetEntity="Comment")
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ShowDj", mappedBy="show")
     */
    private $showDjs;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->showSchedule = new ArrayCollection();
        $this->showDjs = new ArrayCollection();
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

    public function getGenre()
    {
        return $this->genre;
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
    public function createdAt()
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
     * @return string
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
     * @return array
     */
    public function getComments()
    {
        return $this->comments->toArray();
    }

}

