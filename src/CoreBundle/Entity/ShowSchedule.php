<?php

namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ShowSchedule
 *
 * @ORM\Table(name="show_schedule")
 * @ORM\Entity
 */
class ShowSchedule
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
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="time", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="time", nullable=true)
     */
    private $endTime;

    /**
     * @var Show
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="showSchedule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id")
     * })
     */
    private $show;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ShowScheduleMeta", mappedBy="showSchedule")
     */
    private $scheduleMeta;

    public function __construct()
    {
        $this->scheduleMeta = new ArrayCollection();
    }

    public function setShow($show)
    {
        $this->show = $show;
    }

    public function getShow()
    {
        return $this->show;
    }


}

