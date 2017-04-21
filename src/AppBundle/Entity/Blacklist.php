<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blacklist
 *
 * @ORM\Table(name="blacklist")
 * @ORM\Entity
 */
class Blacklist
{
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=300, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;


}

