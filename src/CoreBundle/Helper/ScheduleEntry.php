<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/6/17
 * Time: 10:48 PM
 */

namespace CoreBundle\Helper;


use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Show;
use DateTime;
use Symfony\Component\Cache\CacheItem;
use JMS\Serializer\Annotation As JMS;

class ScheduleEntry
{
    /**
     * @var  DateTime
     * @JMS\Groups({"detail","list"})
     */
    private $showDate;

    /**
     * @var  int
     * @JMS\Groups({"detail","list"})
     */
    private $length;

    /**
     * @var  Show
     * @JMS\Groups({"detail","list"})
     */
    private $show;

    /**
     * @var Schedule
     * @JMS\Groups({"detail","list"})
     */
    private $schedule;

    function __construct(DateTime $showDate,Schedule $schedule)
    {
        $this->showDate = $showDate;
        $this->schedule = $schedule;
        $this->length = $schedule->getShowLength();
        $this->show = $schedule->getShow();

    }

    public function getSchedule()
    {
        return $this->schedule;
    }

    public function getShow()
    {
        return $this->show;
    }

    public function getShowDate()
    {
        return $this->showDate;
    }

    public function getLenght()
    {
        return $this->length;
    }

}