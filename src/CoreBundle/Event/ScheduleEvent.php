<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/6/17
 * Time: 9:03 PM
 */

namespace CoreBundle\Event;


use CoreBundle\Entity\Show;
use Recurr\Rule;

class ScheduleEvent
{
    private  $rule;
    private  $show;

    function __construct(Rule $rule,Show $show)
    {
        $this->rule = $rule;
        $this->show = $show;
    }

    public function getRule()
    {
        return $this->rule;
    }

    public function getShow()
    {
        return $this->show;
    }

}