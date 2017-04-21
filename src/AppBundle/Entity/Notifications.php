<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notifications
 *
 * @ORM\Table(name="notifications")
 * @ORM\Entity
 */
class Notifications
{
    /**
     * @var integer
     *
     * @ORM\Column(name="notificationid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $notificationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="to", type="string", length=400, nullable=false)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=400, nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=false)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="headers", type="string", length=600, nullable=false)
     */
    private $headers;

    /**
     * @var boolean
     *
     * @ORM\Column(name="success", type="boolean", nullable=false)
     */
    private $success;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=false)
     */
    private $note;


}

