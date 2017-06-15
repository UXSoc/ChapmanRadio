<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/15/17
 * Time: 8:35 AM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Schedule;

class ScheduleEvent
{
    private $schedule;
    private $start;
    private $end;

    function __construct(Schedule $schedule,$start,$end)
    {
        $this->schedule = $schedule;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @return Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }
}