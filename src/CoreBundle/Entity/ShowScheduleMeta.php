<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowScheduleMeta
 *
 * @ORM\Table(name="show_schedule_meta", indexes={@ORM\Index(name="event_meta_event_id_fk", columns={"event_id"})})
 * @ORM\Entity
 */
class ShowScheduleMeta
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
     * @ORM\Column(name="meta_key", type="string", length=20, nullable=true)
     */
    private $metaKey;

    /**
     * @var integer
     *
     * @ORM\Column(name="meta_value", type="bigint", nullable=true)
     */
    private $metaValue;

    /**
     * @var \ShowSchedule
     *
     * @ORM\ManyToOne(targetEntity="ShowSchedule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private $event;


}

