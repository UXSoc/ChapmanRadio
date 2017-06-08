<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/29/17
 * Time: 6:55 PM
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role as SRole;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 *
 * @ORM\Table(name="user_role")
 *  @ORM\Entity
 */
class Role extends SRole
{

    const ROLE_STAFF = "ROLE_STAFF";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;


    function __construct($role)
    {
        $this->name = $role;
        parent::__construct($role);
    }

    public function setUser($user)
    {
        $this->user = $user;
    }


    public function setRole($role)
    {
        $this->name = $role;
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
       return $this->name;
    }
}