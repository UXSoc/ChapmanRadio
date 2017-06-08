<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/7/17
 * Time: 7:58 PM
 */

namespace RestfulBundle\Validation;


use Carbon\Carbon;
use Recurr\DateExclusion;
use Recurr\Exception;
use Recurr\Rule;
use Symfony\Component\Validator\Constraints as Assert;

class RRuleType
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="array")
     * @Assert\Choice({"DAILY","WEEKLY","MONTHLY","YEARLY"})
     */
    private $frequency;


    /**
     * @Assert\Type(type="array")
     * @Assert\All({
     *    @Assert\Range(min = 1,max = 12)
     * })
     */
    private $byMonth;

    /**
     * @Assert\Type(type="array")
     * @Assert\Choice({"MO","TU","WE","TH","FR","SA","SU"})
     */
    private $byDay;

    /**
     * @Assert\Type(type="array")
     * @Assert\All({
     *     @Assert\Range(min = 1,max = 53)
     * })
     */
    private $byWeekNumber;

    /**
     * @Assert\Type(type="array")
     * @Assert\All({
     *     @Assert\Range(min = 1,max = 31)
     * })
     */
    private $byMonthDay;

    /**
     * @Assert\Type(type="array")
     * @Assert\All({
     *     @Assert\Range(min = 1,max = 366)
     * })
     */
    private $byYearDay;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $startDate;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $endDate;

    /**
     * @Assert\NotBlank()
     * @Assert\Time()
     */
    private $startTime;

    /**
     * @Assert\NotBlank()
     * @Assert\Time()
     */
    private $endTime;

    /**
     * @Assert\Type(type="array")
     * @Assert\All({
     *    @Assert\Date()
     * })
     */
    private $exceptionDates;

    function __construct()
    {
        $this->byMonth = [];
        $this->byYearDay = [];
        $this->byMonthDay = [];
        $this->byDay = [];
        $this->byWeekNumber = [];

    }

    public function setStartDate($date)
    {
        $this->startDate = $date;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setEndDate($date)
    {
        $this->endDate = $date;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setStartTime($time)
    {
        $this->startTime = $time;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setEndTime($time)
    {
        $this->endTime = $time;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function setByMonth($months)
    {
        $this->byMonth = $months;
    }

    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    }

    public function setByDay($days)
    {
        $this->byDay = $days;
    }

    public function setByWeekNumber($weeks)
    {
        $this->byWeekNumber = $weeks;
    }

    public function setByMonthDay($monthDays)
    {
        $this->byMonthDay = $monthDays;
    }

    public function setByYearDay($yearDays)
    {
        $this->byYearDay = $yearDays;
    }

    public function setExceptionDate($exceptionDate)
    {
        $this->exceptionDates = [];
        foreach ($exceptionDate as $key => $value)
        {
            if($value instanceof DateExclusion)
                $this->exceptionDates[] = $value->date;
            else
                $this->exceptionDates[] = $value;
        }
    }

    public function getExceptionDate()
    {
        return $this->exceptionDates;
    }

    /**
     * @return Rule
     */
    public function getRule()
    {
        $exc = [];
        foreach ($this->exceptionDates as $exception)
        {
            $exc[] = new DateExclusion(Carbon::createFromFormat("YYYY-MM-DD",$exception),false,true);
        }

        return (new Rule())
            ->setFreq($this->frequency)
            ->setByDay($this->byDay)
            ->setByMonth($this->byMonth)
            ->setByWeekNumber($this->byWeekNumber)
            ->setByMonthDay($this->byMonthDay)
            ->setByYearDay($this->byYearDay)
            ->setExDates($exc);
    }

}