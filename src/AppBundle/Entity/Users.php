<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Entity
 */
class Users
{
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
     * @ORM\Column(name="fbid", type="bigint", nullable=false)
     */
    private $fbid;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=200, nullable=false)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="studentid", type="bigint", nullable=false)
     */
    private $studentid;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=30, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="fname", type="string", length=100, nullable=false)
     */
    private $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="lname", type="string", length=100, nullable=false)
     */
    private $lname;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=120, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="djname", type="string", length=120, nullable=false)
     */
    private $djname;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=100, nullable=false)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="seasons", type="string", length=140, nullable=false)
     */
    private $seasons;

    /**
     * @var string
     *
     * @ORM\Column(name="classclub", type="string", nullable=false)
     */
    private $classclub;

    /**
     * @var string
     *
     * @ORM\Column(name="petpreference", type="string", length=255, nullable=false)
     */
    private $petpreference = 'none';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastlogin", type="datetime", nullable=false)
     */
    private $lastlogin;

    /**
     * @var string
     *
     * @ORM\Column(name="lastip", type="string", length=30, nullable=false)
     */
    private $lastip;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=48, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="verifycode", type="string", length=30, nullable=false)
     */
    private $verifycode;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="staffgroup", type="string", length=200, nullable=false)
     */
    private $staffgroup;

    /**
     * @var string
     *
     * @ORM\Column(name="staffposition", type="string", length=200, nullable=false)
     */
    private $staffposition;

    /**
     * @var string
     *
     * @ORM\Column(name="staffemail", type="string", length=200, nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="quizpassedseasons", type="string", length=600, nullable=false)
     */
    private $quizpassedseasons;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionkey", type="string", length=30, nullable=false)
     */
    private $revisionkey;



}

