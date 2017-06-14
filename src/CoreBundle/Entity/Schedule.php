<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Recurr\Rule;

/**
 * ShowSchedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ScheduleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Schedule
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
     * @ORM\Column(name="token", type="string",length=20, nullable=false,unique=true)
     */
    private $token;

    /**
     * @var \DateTime
     * @ORM\Column(name="start_date", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="time", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="time", nullable=true)
     */
    private $endTime;

    /**
     * @var Show
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="schedule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $show;


    /**
     * @var ArrayCollection
     * @ORM\Column(name="meta",type="string")
     */
    private $meta;


    public function __construct()
    {
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        if ( $this->token === null) {
            $this->token = substr(bin2hex(random_bytes(12)),10);
        }
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setShow(Show $show)
    {
        $this->show = $show;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getShow()
    {
        return $this->show;
    }

    public function setStartTime($time)
    {
        $this->startTime = $time;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setEndTime($time)
    {
        $this->endTime = $time;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getMeta()
    {
        $rule =  new Rule($this->meta);
        $rule->setStartDate($this->getStartDate());
        $rule->setEndDate($this->getEndDate());
        return $rule;
    }

    public function setMeta(Rule $meta)
    {
        $meta->setEndDate(null);
        $meta->setStartDate(null);
        $this->meta = $meta->getString();
    }

    public function setStartDate($date)
    {
        $this->startDate = $date;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setEndDate($date)
    {
        $this->endDate = $date;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

}

