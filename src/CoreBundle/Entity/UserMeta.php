<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/31/17
 * Time: 9:48 PM
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * User
 *
 * @ORM\Entity()
 * @ORM\Table(name="user_meta")
 *
 */
class UserMeta
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
     * @ORM\Column(name="meta_key", type="string", length=20, nullable=true)
     */
    private $metaKey;

    /**
     * @var integer
     *
     * @ORM\Column(name="meta_value", type="bigint", nullable=true)
     */
    private $metaValue;

    /**
     * @var Schedule
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="userMeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getMetaKey()
    {
        return $this->metaKey;
    }

    public function setMetaKey($key)
    {
        $this->metaKey = $key;
    }

    public function getMetaValue()
    {
        return $this->metaValue;
    }

    public function setMetaValue($value)
    {
        $this->metaValue = $value;
    }

    public function getUser()
    {
        return $this->user;
    }


}