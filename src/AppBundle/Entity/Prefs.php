<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prefs
 *
 * @ORM\Table(name="prefs")
 * @ORM\Entity
 */
class Prefs
{
    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="val", type="text", nullable=false)
     */
    private $val;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated", type="bigint", nullable=false)
     */
    private $updated;


}

