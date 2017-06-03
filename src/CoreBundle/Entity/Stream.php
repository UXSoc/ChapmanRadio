<?php
namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="stream")
 * @ORM\Entity()
 *
 * @ORM\HasLifecycleCallbacks
 */
class Stream
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
     * @ORM\Column(name="mount", type="string", nullable=false)
     */
    private $mount;

    /**
     * @var string
     * @ORM\Column(name="source", type="string", nullable=false)
     */
    private $recording;

    /**
     * @var Event
     *
     * @ORM\OneToOne(targetEntity="Event", inversedBy="stream")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $event;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        if ($this->createdAt == null) {
            $this->createdAt = new \DateTime('now');
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public  function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setRecording($recording)
    {
        $this->recording = $recording;
    }

    public function getRecording()
    {
        return $this->recording;
    }

    public function getMount()
    {
        return $this->mount;
    }

    public function setMount($mount)
    {
        $this->mount = $mount;
    }


}