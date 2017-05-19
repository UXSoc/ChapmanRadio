<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Show
 *
 * @ORM\Table(name="shows")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ShowRepository")
 */
class Show
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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="blob", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private $score;

    /**
     * @var boolean
     *
     * @ORM\Column(name="profanity", type="boolean", nullable=false)
     */
    private $profanity = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="attendanceoptional", type="boolean", nullable=false)
     */
    private $attendanceoptional = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=80, nullable=true)
     */
    private $genre;

    /**
     * @var integer
     *
     * @ORM\Column(name="header_imge_id", type="bigint", nullable=true)
     */
    private $headerImgeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="strike_count", type="integer", nullable=true)
     */
    private $strikeCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="suspended", type="boolean", nullable=true)
     */
    private $suspended;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_comments", type="boolean", nullable=true)
     */
    private $enableComments = '0';


}

