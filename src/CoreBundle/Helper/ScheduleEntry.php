<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/6/17
 * Time: 10:48 PM.
 */

namespace CoreBundle\Helper;

use CoreBundle\Entity\Show;
use DateTime;

class ScheduleEntry
{
    /** @var DateTime */
    private $date;
    /** @var DateTime */
    private $startTime;
    /** @var DateTime */
    private $endTime;
    /** @var Show */
    private $show;

    public function setShow(Show $show)
    {
        $this->show = $show;
    }

    public function getShow()
    {
        return $this->show;
    }

    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setStartTime($time)
    {
        $this->startTime = $time;
    }

    public function setEndTime($time)
    {
        $this->endTime = $time;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }
}
