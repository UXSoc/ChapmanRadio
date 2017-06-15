<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use RRule\RRule;
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
            new RRule($this->getRule());
        }
        catch (\Exception $e)
        {
            $context->addViolation($e->getMessage());

        }
    }


    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(){
        return $this->createdAt;
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


    /**
     * @param $rule
     * @param $showLength
     */
    public function setRule($rule)
    {
        $this->setFrequency($rule['FREQ']);
        $this->setStartDate($rule['DTSTART']);
        $this->setEndDate($rule['UNTIL']);
        $this->setByHour($rule['BYHOUR']);
        $this->setByMinute($rule['BYMINUTE']);
        $this->setByMonthDay($rule['BYMONTHDAY']);
        $this->setByYearDay($rule['BYYEARDAY']);
        $this->setByDay($rule['BYDAY']);
        $this->setByWeekNumber($rule['BYWEEKNO']);
        $this->setByMonth($rule['BYMONTH']);

    }

    public function getRule()
    {
        return array(
            'DTSTART' => $this->getStartDate(),
            'FREQ' => $this->getFrequency(),
            'UNTIL' => $this->getEndDate(),
            'COUNT' => null,
            'INTERVAL' => 1,
            'BYSECOND' => null,
            'BYMINUTE' => $this->getByMinute(),
            'BYHOUR' => $this->getByHour(),
            'BYDAY' => null,
            'BYMONTHDAY' => $this->getByMonthDay(),
            'BYYEARDAY' => $this->getByYearDay(),
            'BYWEEKNO' => $this->getByWeekNumber(),
            'BYMONTH' => $this->getByMonth(),
            'BYSETPOS' => null,
            'WKST' => 'MO'
        );
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

