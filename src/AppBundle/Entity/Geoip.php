<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Geoip
 *
 * @ORM\Table(name="geoip")
 * @ORM\Entity
 */
class Geoip
{
    /**
     * @var binary
     *
     * @ORM\Column(name="geoip_ip", type="binary", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $geoipIp;

    /**
     * @var string
     *
     * @ORM\Column(name="geoip_countrycode", type="string", length=2, nullable=false)
     */
    private $geoipCountrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="geoip_country", type="string", length=255, nullable=false)
     */
    private $geoipCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="geoip_region", type="string", length=255, nullable=false)
     */
    private $geoipRegion;

    /**
     * @var string
     *
     * @ORM\Column(name="geoip_city", type="string", length=255, nullable=false)
     */
    private $geoipCity;

    /**
     * @var string
     *
     * @ORM\Column(name="geoip_zip", type="string", length=255, nullable=false)
     */
    private $geoipZip;

    /**
     * @var float
     *
     * @ORM\Column(name="geoip_latitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $geoipLatitude;

    /**
     * @var float
     *
     * @ORM\Column(name="geoip_longitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $geoipLongitude;

    /**
     * @var string
     *
     * @ORM\Column(name="geoip_timezone", type="string", length=255, nullable=false)
     */
    private $geoipTimezone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="geoip_lastupdate", type="datetime", nullable=true)
     */
    private $geoipLastupdate;


}

