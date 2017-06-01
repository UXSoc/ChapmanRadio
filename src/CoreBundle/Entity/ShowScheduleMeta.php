<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowScheduleMeta
 *
 * @ORM\Table(name="show_schedule_meta")
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
     * @var ShowSchedule
     *
     * @ORM\ManyToOne(targetEntity="ShowSchedule",inversedBy="scheduleMeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_schedule_id", referencedColumnName="id")
     * })
     */
    private $showSchedule;


    public function getId()
    {
        return $this->id;
    }

    public function getMetaKey()
    {
        return $this->metaKey;
    }

    public function setMetaKey($key)
    {
        $this->metaKey = $key;
    }

    public function getMetaValue()
    {
        return $this->metaValue;
    }

    public function setMetaValue($value)
    {
        $this->metaValue = $value;
    }

    public function getShowSchedule()
    {
        return $this->showSchedule;
    }
}

