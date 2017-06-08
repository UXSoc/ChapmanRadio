<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use BroadcastBundle\Entity\Stream;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\EventRepository")
 */
class Event
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
     * @ORM\Column(name="day", type="date", nullable=false)
     */
    private $current;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="time", nullable=false)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="time", nullable=false)
     */
    private $endTime;

    /**
     * @var Show
     *
     * @ORM\ManyToOne(targetEntity="Show",inversedBy="events")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id",nullable=false)
     * })
     */
    private $show;

    /**
     * @var Stream
     * @ORM\OneToOne(targetEntity="BroadcastBundle\Entity\Stream")
     * @ORM\JoinColumn(name="stream_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $stream;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Stream $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return Stream
     */
    public function getStream()
    {
        return $this->stream;
    }


    public function setStartTime($start)
    {
        $this->startTime = $start;
    }

    /**
     * @return DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setEndTime(DateTime $endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return DateTime
     */
    public  function  getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param DateTime $current
     */
    public function setCurrent(DateTime $current)
    {
        $this->current = $current;
    }

    /**
     * @return Show
     */
    public function getShow()
    {
        return $this->show;
    }



}

