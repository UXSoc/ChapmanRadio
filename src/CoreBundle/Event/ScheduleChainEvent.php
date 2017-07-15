<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/13/17
 * Time: 4:59 PM
 */

namespace CoreBundle\Event;

/*
 * retrieves the current and anything next in the chain based the number
 */
use CoreBundle\Helper\ScheduleEntry;
use Symfony\Component\EventDispatcher\Event;

class ScheduleChainEvent extends  Event
{
    const NAME = "schedule.chain";
    private $count;
    private $includeCurrent;
    private $scheduleEntry;
    private $current;

    function __construct(\DateTime $current, $count, $includeCurrent = true)
    {
        $this->count = $count;
        $this->includeCurrent = $includeCurrent;
        $this->scheduleEntry = [];
        $this->current = $current;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getIncludeCurrent()
    {
        return $this->includeCurrent;
    }

    public function addEntry(ScheduleEntry $entry)
    {
        $this->scheduleEntry[] = $entry;
    }

    public function getScheduleEntries()
    {
        return $this->scheduleEntry;
    }
}