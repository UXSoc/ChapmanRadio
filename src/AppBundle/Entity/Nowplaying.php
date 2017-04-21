<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nowplaying
 *
 * @ORM\Table(name="nowplaying")
 * @ORM\Entity
 */
class Nowplaying
{
    /**
     * @var integer
     *
     * @ORM\Column(name="nowplayingid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $nowplayingid;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="bigint", nullable=false)
     */
    private $showid;

    /**
     * @var string
     *
     * @ORM\Column(name="trackid", type="string", length=36, nullable=false)
     */
    private $trackid;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=600, nullable=false)
     */
    private $text;


}

