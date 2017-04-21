<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Strikes
 *
 * @ORM\Table(name="strikes")
 * @ORM\Entity
 */
class Strikes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="strikeid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $strikeid;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="bigint", nullable=false)
     */
    private $userid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="assignedon", type="datetime", nullable=false)
     */
    private $assignedon;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", nullable=false)
     */
    private $reason;

    /**
     * @var boolean
     *
     * @ORM\Column(name="emailsent", type="boolean", nullable=false)
     */
    private $emailsent = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=10, nullable=false)
     */
    private $season;


}

