<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attendance
 *
 * @ORM\Table(name="attendance")
 * @ORM\Entity
 */
class Attendance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="attendanceid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $attendanceid;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="bigint", nullable=false)
     */
    private $showid;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="bigint", nullable=false)
     */
    private $userid;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="late", type="boolean", nullable=false)
     */
    private $late;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type = 'show';

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=10, nullable=false)
     */
    private $season;


}

