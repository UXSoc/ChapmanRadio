<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity
 */
class Schedule
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="hour", type="boolean", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $hour;

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $season;

    /**
     * @var string
     *
     * @ORM\Column(name="mon", type="string", length=57, nullable=false)
     */
    private $mon = ',';

    /**
     * @var string
     *
     * @ORM\Column(name="tue", type="string", length=57, nullable=false)
     */
    private $tue = ',';

    /**
     * @var string
     *
     * @ORM\Column(name="wed", type="string", length=57, nullable=false)
     */
    private $wed = ',';

    /**
     * @var string
     *
     * @ORM\Column(name="thu", type="string", length=57, nullable=false)
     */
    private $thu = ',';

    /**
     * @var string
     *
     * @ORM\Column(name="fri", type="string", length=57, nullable=false)
     */
    private $fri = ',';

    /**
     * @var string
     *
     * @ORM\Column(name="sat", type="string", length=57, nullable=false)
     */
    private $sat = ',';

    /**
     * @var string
     *
     * @ORM\Column(name="sun", type="string", length=57, nullable=false)
     */
    private $sun = ',';


}

