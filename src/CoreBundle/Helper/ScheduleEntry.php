<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/6/17
 * Time: 10:48 PM
 */

namespace CoreBundle\Helper;


use CoreBundle\Entity\Show;
use DateTime;
use Symfony\Component\Cache\CacheItem;

class ScheduleEntry
{
    /** @var  DateTime */
    private $showDate;

    /** @var  int */
    private $length;

    /** @var  Show */
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
        $this->showDate = $date;
    }

    public function getShowDate()
    {
        return $this->showDate;
    }

    public function getLenght()
    {
        return $this->length;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

}