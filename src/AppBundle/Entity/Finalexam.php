<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Finalexam
 *
 * @ORM\Table(name="finalexam")
 * @ORM\Entity
 */
class Finalexam
{
    /**
     * @var integer
     *
     * @ORM\Column(name="exam_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $examId;

    /**
     * @var integer
     *
     * @ORM\Column(name="exam_user", type="bigint", nullable=false)
     */
    private $examUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="exam_mp3", type="bigint", nullable=false)
     */
    private $examMp3;

    /**
     * @var string
     *
     * @ORM\Column(name="exam_season", type="string", length=10, nullable=false)
     */
    private $examSeason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="exam_created", type="datetime", nullable=false)
     */
    private $examCreated = 'CURRENT_TIMESTAMP';


}

