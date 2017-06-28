<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 10:14 AM
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation As JMS;
/**
 * Profile
 * @ORM\Table(name="profile")
 * @ORM\Entity()
 *
 */
class Profile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Exclude
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User" , inversedBy="profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;


    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=30, nullable=true)
     */
    private $phone;

    /**
     * Many Shows have Many Images.
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="SET NULL"))
     */
    private $image;

    public function getId()
    {
        return $this->id;
    }

    public function getImage(){
        return $this->image;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }


}