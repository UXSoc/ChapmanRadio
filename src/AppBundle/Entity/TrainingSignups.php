<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrainingSignups
 *
 * @ORM\Table(name="training_signups")
 * @ORM\Entity
 */
class TrainingSignups
{
    /**
     * @var integer
     *
     * @ORM\Column(name="trainingsignup_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $trainingsignupId;

    /**
     * @var integer
     *
     * @ORM\Column(name="trainingsignup_slot", type="bigint", nullable=false)
     */
    private $trainingsignupSlot;

    /**
     * @var integer
     *
     * @ORM\Column(name="trainingsignup_userid", type="bigint", nullable=false)
     */
    private $trainingsignupUserid;

    /**
     * @var string
     *
     * @ORM\Column(name="trainingsignup_present", type="string", nullable=false)
     */
    private $trainingsignupPresent = '0';


}

