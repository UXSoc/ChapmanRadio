<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/15/17
 * Time: 8:35 AM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Schedule;
use Symfony\Component\EventDispatcher\Event;

class ScheduleBetweenEvent extends Event
{
    private $schedule;
    private $start;
    private $end;

    private  $datetimes;



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

    public function getDateTimes()
    {
        return $this->datetimes;
    }

    public function hasDates() {
        return count($this->datetimes) > 0;
    }

    /**
     * @param \DateTime[] $dateTimes
     */
    public function setDateTimes($dateTimes)
    {
        $this->datetimes = $dateTimes;
    }
}