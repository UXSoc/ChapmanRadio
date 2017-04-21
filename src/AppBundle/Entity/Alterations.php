<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alterations
 *
 * @ORM\Table(name="alterations")
 * @ORM\Entity
 */
class Alterations
{
    /**
     * @var integer
     *
     * @ORM\Column(name="alterationid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $alterationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="starttimestamp", type="bigint", nullable=false)
     */
    private $starttimestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="endtimestamp", type="bigint", nullable=false)
     */
    private $endtimestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="bigint", nullable=false)
     */
    private $showid;

    /**
     * @var integer
     *
     * @ORM\Column(name="alteredby", type="bigint", nullable=false)
     */
    private $alteredby;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=600, nullable=false)
     */
    private $note;


}

