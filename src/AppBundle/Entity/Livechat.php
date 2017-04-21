<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Livechat
 *
 * @ORM\Table(name="livechat")
 * @ORM\Entity
 */
class Livechat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="livechatid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $livechatid;

    /**
     * @var string
     *
     * @ORM\Column(name="contactid", type="string", length=60, nullable=false)
     */
    private $contactid;

    /**
     * @var string
     *
     * @ORM\Column(name="direction", type="string", nullable=false)
     */
    private $direction;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=1000, nullable=false)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=false)
     */
    private $datetime;


}

