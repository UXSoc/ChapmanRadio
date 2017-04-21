<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shows
 *
 * @ORM\Table(name="shows")
 * @ORM\Entity
 */
class Shows
{
    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $showid;

    /**
     * @var string
     *
     * @ORM\Column(name="showname", type="string", length=100, nullable=false)
     */
    private $showname;

    /**
     * @var string
     *
     * @ORM\Column(name="showtime", type="string", length=255, nullable=false)
     */
    private $showtime;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid1", type="bigint", nullable=false)
     */
    private $userid1;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid2", type="bigint", nullable=false)
     */
    private $userid2;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid3", type="bigint", nullable=false)
     */
    private $userid3;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid4", type="bigint", nullable=false)
     */
    private $userid4;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid5", type="bigint", nullable=false)
     */
    private $userid5;

    /**
     * @var string
     *
     * @ORM\Column(name="seasons", type="string", length=200, nullable=false)
     */
    private $seasons;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=100, nullable=false)
     */
    private $genre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="musictalk", type="string", nullable=false)
     */
    private $musictalk;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp2", type="bigint", nullable=false)
     */
    private $timestamp2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdon", type="date", nullable=false)
     */
    private $createdon;

    /**
     * @var boolean
     *
     * @ORM\Column(name="explicit", type="boolean", nullable=false)
     */
    private $explicit = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="turntables", type="string", nullable=false)
     */
    private $turntables;

    /**
     * @var string
     *
     * @ORM\Column(name="podcastcategory", type="string", length=300, nullable=false)
     */
    private $podcastcategory;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=200, nullable=false)
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="elevation", type="smallint", nullable=false)
     */
    private $elevation = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="swing", type="boolean", nullable=false)
     */
    private $swing = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="ranking", type="boolean", nullable=false)
     */
    private $ranking;

    /**
     * @var boolean
     *
     * @ORM\Column(name="podcastenabled", type="boolean", nullable=false)
     */
    private $podcastenabled = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="clean", type="boolean", nullable=false)
     */
    private $clean = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="attendanceoptional", type="boolean", nullable=false)
     */
    private $attendanceoptional = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="app_differentiate", type="string", length=1200, nullable=false)
     */
    private $appDifferentiate;

    /**
     * @var string
     *
     * @ORM\Column(name="app_promote", type="string", length=1200, nullable=false)
     */
    private $appPromote;

    /**
     * @var string
     *
     * @ORM\Column(name="app_timeline", type="string", length=1200, nullable=false)
     */
    private $appTimeline;

    /**
     * @var string
     *
     * @ORM\Column(name="app_giveaway", type="string", length=1200, nullable=false)
     */
    private $appGiveaway;

    /**
     * @var string
     *
     * @ORM\Column(name="app_speaking", type="string", length=1200, nullable=false)
     */
    private $appSpeaking;

    /**
     * @var string
     *
     * @ORM\Column(name="app_equipment", type="string", length=1200, nullable=false)
     */
    private $appEquipment;

    /**
     * @var string
     *
     * @ORM\Column(name="app_prepare", type="string", length=1200, nullable=false)
     */
    private $appPrepare;

    /**
     * @var string
     *
     * @ORM\Column(name="app_examples", type="string", length=1200, nullable=false)
     */
    private $appExamples;

    /**
     * @var string
     *
     * @ORM\Column(name="availability", type="string", length=1000, nullable=false)
     */
    private $availability;

    /**
     * @var string
     *
     * @ORM\Column(name="availabilitynotes", type="string", length=1200, nullable=false)
     */
    private $availabilitynotes;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionkey", type="string", length=30, nullable=true)
     */
    private $revisionkey;


}

