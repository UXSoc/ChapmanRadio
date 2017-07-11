<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation As JMS;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 *
 * @ORM\Table(name="user")
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(
 *     fields="email",
 *     errorPath="email",
 *     message="email already used")
 *
 * @UniqueEntity(
 *     fields="username",
 *     errorPath="username",
 *     message="username already used")
 */
class User implements AdvancedUserInterface
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
     * @var string
     *
     * @ORM\Column(name="token", type="string",length=20, nullable=false,unique=true)
     * @JMS\Groups({"detail","list"})
     */
    private $token;

    /**
     * @var string
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @Assert\NotBlank
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=500, nullable=false)
     * @JMS\Exclude
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="suspended", type="boolean", nullable=false)
     */
    private $suspended = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $updatedAt;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="username", type="string", length=30, nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $username;

    /**
     * @var boolean
     * @ORM\Column(name="confirmed", type="boolean", nullable=false)
     */
    private $confirmed = 0;


    /**
     * @var Dj
     * @ORM\OneToOne(targetEntity="Dj", mappedBy="user")
     */
    private $dj;


    /**
     * @var Profile
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", cascade={"persist", "detach"})
     */
    private $profile;

    /**
     * @var string
     *
     * @Assert\Regex("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9]/")
     * @Assert\NotBlank()
     * @ORM\Column(name="student_id", type="string", length=15, nullable=false)
     */
    private $studentId;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Role", mappedBy="user", cascade={"persist"})
     */
    private $roles;

    /**
     * @var Schedule
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="UserMeta",mappedBy="user", indexBy="metaKey")
     */
    private $userMeta;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     * @var string
     */
    private $plainTextPassword;




    public function __construct()
    {
        $this->userMeta = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->updatedAt = new \DateTime('now');

        if ($this->createdAt == null) {
            $this->token = substr(bin2hex(random_bytes(12)),10);
            $this->createdAt = new \DateTime('now');
        }
    }

    public function getProfile()
    {
        if($this->profile === null) {
            $this->profile = new Profile();
            $this->profile->setUser($this);
        }
        return $this->profile;
    }

    /**
     * @param UserMeta $meta
     * @return bool
     */
    public function setUserMeta($key, $meta)
    {
        $this->userMeta->set($key,$meta);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getUserMeta($key)
    {
        return $this->userMeta->get($key);
    }


    public function getDj()
    {
        return $this->dj;
    }

    /**
     * @param Dj $dj
     */
    public function setDj(Dj $dj)
    {
        $dj->setUser($this);
        $this->dj = $dj;
    }

    public function isDj()
    {
        return !is_null($this->dj);
    }

    public function updateLastLogin()
    {
        $this->lastLogin = new \DateTime('now');
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Retrieves Chapman Student Id
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Sets the student Id
     * @param $id
     */
    public function setStudentId($id)
    {
        $this->studentId = $id;
    }

    public function setFacebookId($id)
    {
        $this->fbid = $id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function setPlainTextPassword($password)
    {
        $this->plainTextPassword = $password;

    }

    public function getPlainTextPassword()
    {
        return $this->plainTextPassword;
    }



    /**
     * Marks the user as confirmed
     * @param $confirmed
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        $roles = [];
        $roles[] = new \Symfony\Component\Security\Core\Role\Role("ROLE_USER");
        if ($this->isDj())
            $roles[] = new \Symfony\Component\Security\Core\Role\Role("ROLE_DJ");
        $roles = array_merge($roles, $this->roles->toArray());


        return $roles;
    }

    /**
     * @param Role $role
     */
    public function addRole($role)
    {
        $role->setUser($this);
        $this->roles->add($role);
    }


    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * set the user password
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return (!$this->suspended);
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->confirmed;
    }

    /**
     * User token to hide user ids
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * tells if the user is suspended
     * @return bool
     */
    public function isSuspended()
    {
        return $this->suspended;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainTextPassword = '';
    }

}

