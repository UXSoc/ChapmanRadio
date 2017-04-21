<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Locations
 *
 * @ORM\Table(name="locations")
 * @ORM\Entity
 */
class Locations
{
    /**
     * @var integer
     *
     * @ORM\Column(name="location_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $locationId;

    /**
     * @var string
     *
     * @ORM\Column(name="location_zip", type="string", length=255, nullable=false)
     */
    private $locationZip;

    /**
     * @var string
     *
     * @ORM\Column(name="location_city", type="string", length=255, nullable=false)
     */
    private $locationCity;

    /**
     * @var string
     *
     * @ORM\Column(name="location_state", type="string", length=255, nullable=false)
     */
    private $locationState;

    /**
     * @var string
     *
     * @ORM\Column(name="location_country", type="string", length=255, nullable=false)
     */
    private $locationCountry;

    /**
     * @var float
     *
     * @ORM\Column(name="location_latitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $locationLatitude;

    /**
     * @var float
     *
     * @ORM\Column(name="location_longitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $locationLongitude;


}

