<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Recurr\Rule;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ShowSchedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ScheduleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Schedule
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="token", type="string",length=20, nullable=false,unique=true)
     */
    private $token;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_updated", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="start_date", type="date", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="date", nullable=false)
     */
    private $endDate;


    /**
     * @var int
     * @Assert\NotBlank,
     * @Assert\Range(min=0,max=6)
     * @ORM\Column(name="freq", type="integer", nullable=false)
     */
    private $freq = 0;

    /**
     * //as UNIX TIME
     * @var int
     * @ORM\Column(name="show_length", type="integer", nullable=false)
     */
    private $showLength;

    /**
     * @var int
     * @ORM\Column(name="by_hour", type="integer", nullable=false)
     * @Assert\NotBlank,
     * @Assert\Range(min=0,max=23)
     */
    private $byHour;

    /**
     * @var int
     * @ORM\Column(name="by_minute", type="integer", nullable=false)
     * @Assert\NotBlank,
     * @Assert\Range(min=0,max=59)
     */
    private $byMinute;

    /**
     * @ORM\Column(name="by_month_day", type="simple_array", nullable=true)
     * @Assert\All({
     *  @Assert\NotBlank,
     *  @Assert\Range(min=1,max=31)
     * })
     */
    private $byMonthDay;

    /**
     * @var int[]
     * @ORM\Column(name="by_year_day", type="simple_array", nullable=true)
     * @Assert\All({
     *  @Assert\NotBlank,
     *  @Assert\Range(min=1,max=366)
     * })
     */
    private $byYearDay;

    /**
     * @var int[]
     * @ORM\Column(name="by_day", type="simple_array", nullable=true)
     * @Assert\Choice({"MO", "TU", "WE", "TH", "FR", "SA", "SU"})
     */
    private $byDay;

    /**
     * @var int[]
     * @Assert\All({
     *  @Assert\NotBlank,
     *  @Assert\Range(min=1,max=53)
     * })
     * @ORM\Column(name="by_week_number", type="simple_array", nullable=true)
     */
    private $byWeekNumber;


    /**
     * @var int[]
     * @Assert\All({
     *  @Assert\NotBlank,
     *  @Assert\Range(min=1,max=12)
     * })
     * @ORM\Column(name="by_month", type="simple_array", nullable=true)
     */
    private $byMonth;


    /**
     * @var Show
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="schedule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $show;


    public function __construct()
    {
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->updatedAt = new \DateTime('now');

        if ($this->createdAt === null) {
            $this->token = substr(bin2hex(random_bytes(12)), 10);
            $this->createdAt = new \DateTime('now');
        }
    }


    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        try{
            $this->getRule();
        }
        catch (\Exception $e)
        {
            $context->addViolation($e->getMessage());

        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStartTime()
    {
        return (new Carbon())->setTime($this->byHour,$this->byMinute);
    }

    public function getEndTime()
    {
        return $this->getStartTime()->addSecond($this->showLength);
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setShow(Show $show)
    {
        $this->show = $show;
    }


    public function getShow()
    {
        return $this->show;
    }

    public function setStartTime($time)
    {
        $this->startTime = $time;
    }


    public function setEndTime($time)
    {
        $this->endTime = $time;
    }


    public function setRule(Rule $rule,$showLength)
    {
        $this->showLength = $showLength;
        $this->setFrequency($rule->getFreq() !== null ? $rule->getFreq() : 0);
        $this->setStartDate($rule->getStartDate());
        $this->setEndDate($rule->getEndDate());
        $this->setByHour($rule->getByHour()[0]);
        $this->setByMinute($rule->getByMinute()[0]);
        $this->setByMonthDay($rule->getByMonthDay());
        $this->setByYearDay($rule->getByYearDay());
        $this->setByDay($rule->getByDay());
        $this->setByWeekNumber($rule->getByWeekNumber());
        $this->setByMonth($rule->getByMonth());

    }

    public function getRule()
    {
        $rule = new Rule();

        $rule->setFreq($this->freq);
        $rule->setStartDate($this->getStartDate());
        $rule->setEndDate($this->getEndDate());
        $rule->setByHour([$this->byHour]);
        $rule->setByMinute([$this->byMinute]);
        if (count($this->byMonthDay) > 0)
            $rule->setByMonthDay($this->byMonthDay);
        if (count($this->byYearDay) > 0)
            $rule->setByYearDay($this->byYearDay);
        if (count($this->byDay) > 0)
            $rule->setByDay($this->byDay);
        if (count($this->byWeekNumber) > 0)
            $rule->setByWeekNumber($this->byWeekNumber);
        if (count($this->byMonth) > 0)
            $rule->setByMonth($this->byMonth);

        return $rule;
    }

    public function setByDay($byday)
    {
        $this->byDay = $byday;
    }



    /**
     * Specifies the first instance in the reccurence set
     * @param $date
     */
    public function setStartDate($date)
    {
        $this->startDate = $date;
    }

    /**
     * the last possible instance in the reccurence set
     * @param $date
     */
    public function setEndDate($date)
    {
        $this->endDate = $date;
    }

    public function getFrequency()
    {
        return $this->freq;
    }

    public function setFrequency($frequency)
    {
        $this->freq = $frequency;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getShowLength()
    {
        return $this->showLength;
    }

    public function setShowLenght($length)
    {
        $this->showLength = $length;
    }

    public function getByHour()
    {
        return $this->byHour;
    }

    public function setByHour($hour)
    {
        $this->byHour = $hour;
    }

    public function getByMinute()
    {
        return $this->byMinute;
    }


    public function setByMinute($minute)
    {
        $this->byMinute = $minute;
    }

    public function getByMonthDay()
    {
        return $this->byMonthDay;
    }

    public function setByMonthDay($byMonthDay)
    {
        $this->byMonthDay = $byMonthDay;
    }

    /**
     * @param array $days
     */
    public function setByYearDay($days)
    {
        $this->byYearDay = $days;
    }

    public function getByYearDay()
    {
        return $this->byYearDay;
    }

    public function getByWeekNumber()
    {
        return $this->byWeekNumber;
    }

    /**
     * @param array $byWeekNumber
     */
    public function setByWeekNumber($byWeekNumber)
    {
        $this->byWeekNumber = $byWeekNumber;
    }

    /**
     * @param array $byMonth
     */
    public function setByMonth($byMonth)
    {
        $this->byMonth = $byMonth;
    }

    public function getByMonth()
    {
        return $this->byMonth;
    }

}

