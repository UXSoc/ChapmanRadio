<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dj
 *
 * @ORM\Table(name="dj", uniqueConstraints={@ORM\UniqueConstraint(name="dj_user_id_uindex", columns={"user_id"})})
 * @ORM\Entity
 */
class Dj
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="blob", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="strike_count", type="integer", nullable=true)
     */
    private $strikeCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="attend_workshop", type="boolean", nullable=true)
     */
    private $attendWorkshop;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


}

