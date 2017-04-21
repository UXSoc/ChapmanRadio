<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Genrecontent
 *
 * @ORM\Table(name="genrecontent")
 * @ORM\Entity
 */
class Genrecontent
{
    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=40, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $genre;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=6000, nullable=false)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="staffid", type="bigint", nullable=false)
     */
    private $staffid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastmodified", type="datetime", nullable=false)
     */
    private $lastmodified;


}

