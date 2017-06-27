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