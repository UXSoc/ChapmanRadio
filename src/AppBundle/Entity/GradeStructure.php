<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GradeStructure
 *
 * @ORM\Table(name="grade_structure")
 * @ORM\Entity
 */
class GradeStructure
{
    /**
     * @var integer
     *
     * @ORM\Column(name="grade_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $gradeId;

    /**
     * @var string
     *
     * @ORM\Column(name="grade_name", type="string", length=255, nullable=false)
     */
    private $gradeName;

    /**
     * @var string
     *
     * @ORM\Column(name="grade_type", type="string", nullable=false)
     */
    private $gradeType;

    /**
     * @var integer
     *
     * @ORM\Column(name="grade_parent", type="integer", nullable=true)
     */
    private $gradeParent;

    /**
     * @var string
     *
     * @ORM\Column(name="grade_season", type="string", length=10, nullable=false)
     */
    private $gradeSeason;

    /**
     * @var string
     *
     * @ORM\Column(name="grade_condition", type="string", nullable=false)
     */
    private $gradeCondition;

    /**
     * @var integer
     *
     * @ORM\Column(name="grade_max", type="integer", nullable=false)
     */
    private $gradeMax;

    /**
     * @var integer
     *
     * @ORM\Column(name="grade_target", type="integer", nullable=true)
     */
    private $gradeTarget;


}

