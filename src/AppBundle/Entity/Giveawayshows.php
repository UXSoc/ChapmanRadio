<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Giveawayshows
 *
 * @ORM\Table(name="giveawayshows")
 * @ORM\Entity
 */
class Giveawayshows
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="giveawayid", type="integer", nullable=false)
     */
    private $giveawayid;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="integer", nullable=false)
     */
    private $showid;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="integer", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="winner", type="string", length=150, nullable=false)
     */
    private $winner;


}

