<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrainingSlots
 *
 * @ORM\Table(name="training_slots")
 * @ORM\Entity
 */
class TrainingSlots
{
    /**
     * @var integer
     *
     * @ORM\Column(name="trainingslot_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $trainingslotId;

    /**
     * @var string
     *
     * @ORM\Column(name="trainingslot_season", type="string", length=6, nullable=false)
     */
    private $trainingslotSeason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trainingslot_datetime", type="datetime", nullable=false)
     */
    private $trainingslotDatetime;

    /**
     * @var integer
     *
     * @ORM\Column(name="trainingslot_staffid", type="bigint", nullable=false)
     */
    private $trainingslotStaffid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="trainingslot_max", type="boolean", nullable=false)
     */
    private $trainingslotMax;


}

