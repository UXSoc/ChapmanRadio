<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emaillists
 *
 * @ORM\Table(name="emaillists")
 * @ORM\Entity
 */
class Emaillists
{
    /**
     * @var integer
     *
     * @ORM\Column(name="emaillistid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $emaillistid;

    /**
     * @var string
     *
     * @ORM\Column(name="listname", type="string", length=100, nullable=false)
     */
    private $listname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=600, nullable=false)
     */
    private $email;


}

