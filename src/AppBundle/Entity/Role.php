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

    /**
     * @var integer
     *
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="roles")
     * @ORM\JoinColumn(name="id", referencedColumnName="userid")
     */
    private $user;


    /**
     * @var string
     * @ORM\Column(name="role", type="string", length=30, nullable=false)
     */
    private $role;




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
}