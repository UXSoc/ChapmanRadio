<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stats
 *
 * @ORM\Table(name="stats")
 * @ORM\Entity
 */
class Stats
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $datetime;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="bigint", nullable=false)
     */
    private $showid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="chapmanradio", type="smallint", nullable=false)
     */
    private $chapmanradio = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="chapmanradiolowquality", type="smallint", nullable=false)
     */
    private $chapmanradiolowquality = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="sports", type="smallint", nullable=false)
     */
    private $sports = '0';


}

