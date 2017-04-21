<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Announcements
 *
 * @ORM\Table(name="announcements")
 * @ORM\Entity
 */
class Announcements
{
    /**
     * @var integer
     *
     * @ORM\Column(name="announcementid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $announcementid;

    /**
     * @var string
     *
     * @ORM\Column(name="twitterid", type="string", length=400, nullable=false)
     */
    private $twitterid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="announcedon", type="datetime", nullable=false)
     */
    private $announcedon;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=600, nullable=false)
     */
    private $message;


}

