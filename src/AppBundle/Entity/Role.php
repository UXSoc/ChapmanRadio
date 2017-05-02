<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/1/17
 * Time: 10:03 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;


/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */
class Role implements RoleInterface
{
    const USER_ROLE = 'ROLE_USER';
    const STAFF_ROLE = 'ROLE_STAFF';
    const DJ_ROLE = 'ROLE_DJ';
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="roles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="userid")
     */
    private $user;


    /**
     * @var string
     * @ORM\Column(name="role", type="string", length=30, nullable=false)
     */
    private $role;


    public  function setUser($user)
    {
        $this->user = $user;
    }

    public  function  setRole($role)
    {
        $this->role = $role;
    }


    /**
     * Returns the role.
     *
     * This method returns a string representation whenever possible.
     *
     * When the role cannot be represented with sufficient precision by a
     * string, it should return null.
     *
     * @return string|null A string representation of the role, or null
     */
    public function getRole()
    {
        return $this->role;
    }

    public function __toString()
    {
        return $this->role; // if you have a name property you can do $this->getName();
    }
}