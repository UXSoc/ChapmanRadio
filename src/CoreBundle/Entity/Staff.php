<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/18/17
 * Time: 5:37 PM
 */

namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *  @ORM\Table(name="staff")
 *  @ORM\Entity
 */
class Staff
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
     * @var \User
     *
     * @ORM\OneToOne(targetEntity="User",inversedBy="staff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public  function setUser($user)
    {
        $this->user = $user;
    }

    public  function  getUser()
    {
        return  $this->user;
    }

    public  function getId()
    {
        return $this->id;
    }
}