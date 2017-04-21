<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Calendar
 *
 * @ORM\Table(name="calendar")
 * @ORM\Entity
 */
class Calendar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="calendar_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $calendarId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="calendar_datetime", type="datetime", nullable=false)
     */
    private $calendarDatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="calendar_text", type="string", length=255, nullable=false)
     */
    private $calendarText;

    /**
     * @var string
     *
     * @ORM\Column(name="calendar_type", type="string", nullable=false)
     */
    private $calendarType = 'public';


}

