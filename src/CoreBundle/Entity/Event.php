<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event", uniqueConstraints={@ORM\UniqueConstraint(name="event_id_uindex", columns={"id"})}, indexes={@ORM\Index(name="_event_show_id_fk", columns={"show_id"}), @ORM\Index(name="_event_show_schedule_id_fk", columns={"show_schedule_id"})})
 * @ORM\Entity
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
     * @ORM\Column(name="start", type="datetime", nullable=true)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id")
     * })
     */
    private $show;

    /**
     * @var \ShowSchedule
     *
     * @ORM\ManyToOne(targetEntity="ShowSchedule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_schedule_id", referencedColumnName="id")
     * })
     */
    private $showSchedule;


}

