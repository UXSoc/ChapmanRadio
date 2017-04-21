<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GradeValues
 *
 * @ORM\Table(name="grade_values")
 * @ORM\Entity
 */
class GradeValues
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="grade_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $gradeId;

    /**
     * @var float
     *
     * @ORM\Column(name="grade_value", type="float", precision=10, scale=0, nullable=false)
     */
    private $gradeValue;


}

