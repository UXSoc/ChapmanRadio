<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoipOld
 *
 * @ORM\Table(name="geoip_old")
 * @ORM\Entity
 */
class GeoipOld
{
    /**
     * @var integer
     *
     * @ORM\Column(name="geoipid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $geoipid;

    /**
     * @var integer
     *
     * @ORM\Column(name="ip1", type="integer", nullable=false)
     */
    private $ip1;

    /**
     * @var integer
     *
     * @ORM\Column(name="ip2", type="integer", nullable=false)
     */
    private $ip2;

    /**
     * @var integer
     *
     * @ORM\Column(name="ip3", type="integer", nullable=false)
     */
    private $ip3;

    /**
     * @var integer
     *
     * @ORM\Column(name="ip4", type="integer", nullable=false)
     */
    private $ip4;

    /**
     * @var string
     *
     * @ORM\Column(name="countrycode", type="string", length=10, nullable=false)
     */
    private $countrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=200, nullable=false)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=200, nullable=false)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=200, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=10, nullable=false)
     */
    private $zip;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float", precision=10, scale=6, nullable=false)
     */
    private $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="lng", type="float", precision=10, scale=6, nullable=false)
     */
    private $lng;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=14, nullable=false)
     */
    private $timezone;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastsync", type="bigint", nullable=false)
     */
    private $lastsync;

    /**
     * @var integer
     *
     * @ORM\Column(name="total", type="integer", nullable=false)
     */
    private $total = '0';


}

