<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quizquestions
 *
 * @ORM\Table(name="quizquestions")
 * @ORM\Entity
 */
class Quizquestions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="quizquestionid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $quizquestionid;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=600, nullable=false)
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="responses", type="string", length=4000, nullable=false)
     */
    private $responses;

    /**
     * @var string
     *
     * @ORM\Column(name="full", type="string", length=400, nullable=false)
     */
    private $full;

    /**
     * @var string
     *
     * @ORM\Column(name="pic", type="string", length=400, nullable=false)
     */
    private $pic;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=400, nullable=false)
     */
    private $icon;

    /**
     * @var integer
     *
     * @ORM\Column(name="createdby", type="bigint", nullable=false)
     */
    private $createdby;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';


}

