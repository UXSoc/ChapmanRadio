<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Events
 *
 * @ORM\Table(name="events")
 * @ORM\Entity
 */
class Events
{
    /**
     * @var integer
     *
     * @ORM\Column(name="eventid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $eventid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=400, nullable=false)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=600, nullable=false)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=600, nullable=false)
     */
    private $link;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="primaryeventpicid", type="bigint", nullable=false)
     */
    private $primaryeventpicid = '0';


}

