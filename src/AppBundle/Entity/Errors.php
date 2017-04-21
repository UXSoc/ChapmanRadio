<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Errors
 *
 * @ORM\Table(name="errors")
 * @ORM\Entity
 */
class Errors
{
    /**
     * @var integer
     *
     * @ORM\Column(name="errorid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $errorid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=32, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", nullable=false)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="referer", type="string", length=300, nullable=false)
     */
    private $referer;

    /**
     * @var string
     *
     * @ORM\Column(name="useragent", type="string", length=300, nullable=false)
     */
    private $useragent;


}

