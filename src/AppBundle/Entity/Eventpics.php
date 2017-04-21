<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eventpics
 *
 * @ORM\Table(name="eventpics")
 * @ORM\Entity
 */
class Eventpics
{
    /**
     * @var integer
     *
     * @ORM\Column(name="eventpicid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $eventpicid;

    /**
     * @var integer
     *
     * @ORM\Column(name="eventid", type="bigint", nullable=false)
     */
    private $eventid;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=600, nullable=false)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="pic", type="string", length=600, nullable=false)
     */
    private $pic;

    /**
     * @var string
     *
     * @ORM\Column(name="full", type="string", length=600, nullable=false)
     */
    private $full;

    /**
     * @var string
     *
     * @ORM\Column(name="caption", type="string", length=2000, nullable=false)
     */
    private $caption;


}

