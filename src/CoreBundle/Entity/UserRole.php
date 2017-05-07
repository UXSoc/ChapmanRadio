<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * UserRole
 *
 * @ORM\Table(name="user_role", indexes={@ORM\Index(name="role_users_id_fk", columns={"user_id"})})
 * @ORM\Entity
 */
class UserRole implements RoleInterface
{

    const USER_ROLE = 'ROLE_USER';
    const STAFF_ROLE = 'ROLE_STAFF';
    const DJ_ROLE = 'ROLE_DJ';

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
     * @ORM\Column(name="role", type="string", length=30, nullable=false)
     */
    private $role;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


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

