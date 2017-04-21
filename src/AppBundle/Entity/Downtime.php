<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Downtime
 *
 * @ORM\Table(name="downtime")
 * @ORM\Entity
 */
class Downtime
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
     * @var boolean
     *
     * @ORM\Column(name="icecastisdown", type="boolean", nullable=false)
     */
    private $icecastisdown = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="chapmanradioisdown", type="boolean", nullable=false)
     */
    private $chapmanradioisdown = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="chapmanradiolowqualityisdown", type="boolean", nullable=false)
     */
    private $chapmanradiolowqualityisdown = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="notified", type="boolean", nullable=false)
     */
    private $notified = '0';


}

