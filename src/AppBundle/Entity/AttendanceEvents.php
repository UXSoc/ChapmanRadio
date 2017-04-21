<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AttendanceEvents
 *
 * @ORM\Table(name="attendance_events")
 * @ORM\Entity
 */
class AttendanceEvents
{
    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="eventname", type="string", length=400, nullable=false)
     */
    private $eventname;

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=6, nullable=false)
     */
    private $season;


}

