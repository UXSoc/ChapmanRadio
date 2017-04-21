<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScheduleTemp
 *
 * @ORM\Table(name="schedule_temp")
 * @ORM\Entity
 */
class ScheduleTemp
{
    /**
     * @var integer
     *
     * @ORM\Column(name="schedule_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $scheduleId;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule_season", type="string", length=6, nullable=false)
     */
    private $scheduleSeason;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule_day", type="string", nullable=false)
     */
    private $scheduleDay;

    /**
     * @var boolean
     *
     * @ORM\Column(name="schedule_hour", type="boolean", nullable=false)
     */
    private $scheduleHour;

    /**
     * @var boolean
     *
     * @ORM\Column(name="schedule_weekoffset", type="boolean", nullable=false)
     */
    private $scheduleWeekoffset;

    /**
     * @var integer
     *
     * @ORM\Column(name="schedule_show_id", type="integer", nullable=false)
     */
    private $scheduleShowId;


}

