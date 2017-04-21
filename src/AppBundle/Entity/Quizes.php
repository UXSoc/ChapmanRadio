<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quizes
 *
 * @ORM\Table(name="quizes")
 * @ORM\Entity
 */
class Quizes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="quizid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $quizid;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="bigint", nullable=false)
     */
    private $userid;

    /**
     * @var integer
     *
     * @ORM\Column(name="startedon", type="bigint", nullable=false)
     */
    private $startedon;

    /**
     * @var string
     *
     * @ORM\Column(name="q1", type="string", length=600, nullable=false)
     */
    private $q1;

    /**
     * @var string
     *
     * @ORM\Column(name="q2", type="string", length=600, nullable=false)
     */
    private $q2;

    /**
     * @var string
     *
     * @ORM\Column(name="q3", type="string", length=600, nullable=false)
     */
    private $q3;

    /**
     * @var string
     *
     * @ORM\Column(name="q4", type="string", length=600, nullable=false)
     */
    private $q4;

    /**
     * @var string
     *
     * @ORM\Column(name="q5", type="string", length=600, nullable=false)
     */
    private $q5;

    /**
     * @var string
     *
     * @ORM\Column(name="q6", type="string", length=600, nullable=false)
     */
    private $q6;

    /**
     * @var string
     *
     * @ORM\Column(name="q7", type="string", length=600, nullable=false)
     */
    private $q7;

    /**
     * @var string
     *
     * @ORM\Column(name="q8", type="string", length=600, nullable=false)
     */
    private $q8;

    /**
     * @var string
     *
     * @ORM\Column(name="q9", type="string", length=600, nullable=false)
     */
    private $q9;

    /**
     * @var string
     *
     * @ORM\Column(name="q10", type="string", length=600, nullable=false)
     */
    private $q10;

    /**
     * @var boolean
     *
     * @ORM\Column(name="completed", type="boolean", nullable=false)
     */
    private $completed = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="right", type="boolean", nullable=false)
     */
    private $right;

    /**
     * @var boolean
     *
     * @ORM\Column(name="wrong", type="boolean", nullable=false)
     */
    private $wrong;

    /**
     * @var boolean
     *
     * @ORM\Column(name="total", type="boolean", nullable=false)
     */
    private $total;


}

