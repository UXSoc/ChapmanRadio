<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Giveaways
 *
 * @ORM\Table(name="giveaways")
 * @ORM\Entity
 */
class Giveaways
{
    /**
     * @var integer
     *
     * @ORM\Column(name="giveawayid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $giveawayid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=600, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text", nullable=false)
     */
    private $about;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=600, nullable=false)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="howtowin", type="string", length=1000, nullable=false)
     */
    private $howtowin;

    /**
     * @var binary
     *
     * @ORM\Column(name="hometext", type="binary", nullable=false)
     */
    private $hometext;

    /**
     * @var string
     *
     * @ORM\Column(name="shows", type="string", length=600, nullable=false)
     */
    private $shows;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expireson", type="date", nullable=false)
     */
    private $expireson;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="revisionkey", type="string", length=30, nullable=false)
     */
    private $revisionkey;


}

