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
    private $strikeCount = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="attend_workshop", type="boolean", nullable=true)
     */
    private $attendWorkshop = false;

    /**
     * @var \User
     *
     * @ORM\OneToOne(targetEntity="User" , inversedBy="dj")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public  function getId()
    {
        return $this->id;
    }

    public  function setUser($user)
    {
        $this->user = $user;
    }

    public  function  getUser()
    {
        return $this->user;
    }

    public  function setDescription($description){
        $this->description = $description;
    }

    public  function  getDescription()
    {
        return $this->description;
    }

    public  function  getStrikeCount()
    {
        return $this->strikeCount;
    }

    public  function  setStrikeCount($count)
    {
        $this->strikeCount = $count;
    }

    public  function setAttendWorkshop($workshop)
    {
        $this->attendWorkshop = $workshop;
    }

    public  function  getAttendWorkshop()
    {
        return $this->attendWorkshop;
    }

}

