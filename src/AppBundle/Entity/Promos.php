<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Promos
 *
 * @ORM\Table(name="promos")
 * @ORM\Entity
 */
class Promos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="promoid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $promoid;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=200, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=140, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expireson", type="datetime", nullable=false)
     */
    private $expireson;


}

