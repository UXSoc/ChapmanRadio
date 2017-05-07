<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recording
 *
 * @ORM\Table(name="recording", indexes={@ORM\Index(name="recording_event_id_fk", columns={"event_id"}), @ORM\Index(name="recording_show_id_fk", columns={"show_id"})})
 * @ORM\Entity
 */
class Recording
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=100, nullable=true)
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=80, nullable=true)
     */
    private $shortName;

    /**
     * @var integer
     *
     * @ORM\Column(name="downloads", type="integer", nullable=true)
     */
    private $downloads;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private $event;

    /**
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id")
     * })
     */
    private $show;


}

