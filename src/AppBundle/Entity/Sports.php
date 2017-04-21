<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sports
 *
 * @ORM\Table(name="sports")
 * @ORM\Entity
 */
class Sports
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
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="onair", type="boolean", nullable=false)
     */
    private $onair;

    /**
     * @var string
     *
     * @ORM\Column(name="sport", type="string", length=255, nullable=false)
     */
    private $sport;

    /**
     * @var string
     *
     * @ORM\Column(name="gamename", type="string", length=255, nullable=false)
     */
    private $gamename;

    /**
     * @var string
     *
     * @ORM\Column(name="ourname", type="string", length=255, nullable=false)
     */
    private $ourname;

    /**
     * @var string
     *
     * @ORM\Column(name="theirname", type="string", length=255, nullable=false)
     */
    private $theirname;

    /**
     * @var integer
     *
     * @ORM\Column(name="ourscore", type="smallint", nullable=false)
     */
    private $ourscore;

    /**
     * @var integer
     *
     * @ORM\Column(name="theirscore", type="smallint", nullable=false)
     */
    private $theirscore;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", length=16777215, nullable=false)
     */
    private $notes;


}

