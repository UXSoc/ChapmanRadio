<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Staff
 *
 * @ORM\Table(name="staff")
 * @ORM\Entity
 */
class Staff
{
    /**
     * @var integer
     *
     * @ORM\Column(name="staffid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $staffid;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="bigint", nullable=false)
     */
    private $userid;

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=10, nullable=false)
     */
    private $season;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=120, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="perms", type="string", length=100, nullable=false)
     */
    private $perms;


}

