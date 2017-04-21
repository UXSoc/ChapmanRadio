<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Awards
 *
 * @ORM\Table(name="awards")
 * @ORM\Entity
 */
class Awards
{
    /**
     * @var integer
     *
     * @ORM\Column(name="awardid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $awardid;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=75, nullable=false)
     */
    private $type = 'showoftheweek';

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="bigint", nullable=false)
     */
    private $showid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="awardedon", type="date", nullable=false)
     */
    private $awardedon;

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=12, nullable=false)
     */
    private $season;


}

