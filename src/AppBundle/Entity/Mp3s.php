<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mp3s
 *
 * @ORM\Table(name="mp3s")
 * @ORM\Entity
 */
class Mp3s
{
    /**
     * @var integer
     *
     * @ORM\Column(name="mp3id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mp3id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=150, nullable=false)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="bigint", nullable=false)
     */
    private $showid;

    /**
     * @var string
     *
     * @ORM\Column(name="shortname", type="string", length=75, nullable=false)
     */
    private $shortname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recordedon", type="datetime", nullable=false)
     */
    private $recordedon;

    /**
     * @var integer
     *
     * @ORM\Column(name="downloads", type="integer", nullable=false)
     */
    private $downloads = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="streams", type="integer", nullable=false)
     */
    private $streams = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="podcasts", type="integer", nullable=false)
     */
    private $podcasts = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=100, nullable=false)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="moreinfo", type="string", length=100, nullable=false)
     */
    private $moreinfo;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=750, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=10, nullable=false)
     */
    private $season;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="clean", type="boolean", nullable=false)
     */
    private $clean = '0';


}

