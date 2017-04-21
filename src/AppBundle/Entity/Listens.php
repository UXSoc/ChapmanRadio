<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Listens
 *
 * @ORM\Table(name="listens")
 * @ORM\Entity
 */
class Listens
{
    /**
     * @var integer
     *
     * @ORM\Column(name="listen_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $listenId;

    /**
     * @var integer
     *
     * @ORM\Column(name="recording_id", type="integer", nullable=false)
     */
    private $recordingId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", nullable=false)
     */
    private $source = 'unknown';

    /**
     * @var binary
     *
     * @ORM\Column(name="ipaddr", type="binary", nullable=false)
     */
    private $ipaddr;


}

