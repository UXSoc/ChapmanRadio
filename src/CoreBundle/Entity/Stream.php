<?php
namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="stream")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\StreamRepository")
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
     * @ORM\Column(name="username", type="string",length=20, nullable=false,unique=true)
     *
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(name="token", type="string",length=20, nullable=false,unique=true)
     *
     */
    private $password;

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
        if ($this->createdAt === null) {
            $this->token = substr(bin2hex(random_bytes(12)),10);
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

    public function getRecording()
    {
        return $this->recording;
    }

    public function setRecording($recording)
    {
        $this->recording = $recording;
    }

    public function getMount()
    {
        return $this->mount;
    }

    public function setMount($mount)
    {
        $this->mount = $mount;
    }

    public function setUsername($name)
    {
        $this->username = $name;
    }

    public function getUsername()
    {
        return $this->username;
    }


    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}