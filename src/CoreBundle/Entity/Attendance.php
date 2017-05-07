<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attendance
 *
 * @ORM\Table(name="attendance", indexes={@ORM\Index(name="attendance_event_id_fk", columns={"event_id"}), @ORM\Index(name="attendance_strike_id_fk", columns={"strike_id"})})
 * @ORM\Entity
 */
class Attendance
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
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="late", type="time", nullable=true)
     */
    private $late;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private $event;

    /**
     * @var \Strike
     *
     * @ORM\ManyToOne(targetEntity="Strike")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="strike_id", referencedColumnName="id")
     * })
     */
    private $strike;


}

