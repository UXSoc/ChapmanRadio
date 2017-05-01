<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Users
 *
 * @ORM\Table(name="users")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class Users implements AdvancedUserInterface
{
    const USER_ROLE = 'USER_ROLE';
    const STAFF_ROLE = 'STAFF_ROLE';
    const DJ_ROLE = 'DJ_ROLE';

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userid;

    /**
     * @var integer
     *
     * @ORM\Column(name="fbid", type="bigint", nullable=true)
     */
    private $fbid;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=200, nullable=false , unique=true)
     */
    private $email;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @ORM\Column(name="studentid", type="bigint", nullable=false)
     */
    private $studentid;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=30, nullable=true)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=120, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=30, nullable=false, unique=true)
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(name="djname", type="string", length=120, nullable=true)
     */
    private $djname;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=100, nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="seasons", type="string", length=140, nullable=true)
     */
    private $seasons;

    /**
     * @var string
     *
     * @ORM\Column(name="classclub", type="string", nullable=false)
     */
    private $classclub = 'club';

    /**
     * @var string
     *
     * @ORM\Column(name="petpreference", type="string", length=255, nullable=false)
     */
    private $petpreference = 'none';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastlogin", type="datetime", nullable=true)
     */
    private $lastlogin ;

    /**
     * @var string
     *
     * @ORM\Column(name="lastip", type="string", length=30, nullable=true)
     */
    private $lastip;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string", length=30, nullable=true)
     */
    private $confirmation_token;



    /**
     * @var string
     *
     * @ORM\Column(name="staffgroup", type="string", length=200, nullable=true)
     */
    private $staffgroup;

    /**
     * @var string
     *
     * @ORM\Column(name="staffposition", type="string", length=200, nullable=true)
     */
    private $staffposition;

    /**
     * @var string
     *
     * @ORM\Column(name="staffemail", type="string", length=200, nullable=true)
     */
    private $staffemail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmnewsletter", type="boolean", nullable=false)
     */
    private $confirmnewsletter = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="workshoprequired", type="boolean", nullable=false)
     */
    private $workshoprequired = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="suspended", type="boolean", nullable=false)
     */
    private $suspended = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmed", type="boolean", nullable=false)
     */
    private $confirmed = '0';


    /**
     * @var string
     *
     * @ORM\Column(name="revisionkey", type="string", length=30, nullable=false)
     */
    private $revisionkey = '';


    /**
     * @var array
     *
     * @ORM\Column(name="role", type="simple_array", nullable=false)
     */
    private $role = [Users::USER_ROLE];



    public function getId()
    {
        return $this->userid;
    }

    public  function getName()
    {
        return $this->name;
    }

    public  function setName($name)
    {
        $this->name = $name;
    }

    public  function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUserId()
    {
        return $this->userid;
    }

    public  function  setFacebookId($id)
    {
        $this->fbid = $id;
    }


    public  function  getEmail()
    {
        return $this->email;
    }

    public  function  setEmail($email)
    {
        $this->email = $email;
    }

    public  function  getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function  getStudentid()
    {
        return $this->studentid;
    }

    public  function setStudentId($studentId)
    {
        $this->studentid = $studentId;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public  function getConfirmationToken()
    {
        return $this->confirmation_token;
    }


    public  function  setConfirmationToken($confirmation_token)
    {
        $this->confirmation_token = $confirmation_token;
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
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->role;
    }

    public function setRoles($roles)
    {
        $this->role = $roles;
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

    public  function setPassword($password)
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

    public function setConfirmed($confirm)
    {
        return $this->confirmed = $confirm;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = "";
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
}

