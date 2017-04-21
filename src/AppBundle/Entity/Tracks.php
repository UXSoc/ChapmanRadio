<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tracks
 *
 * @ORM\Table(name="tracks", indexes={@ORM\Index(name="track_name", columns={"track_name"}), @ORM\Index(name="artist_name", columns={"artist_name"}), @ORM\Index(name="artist_id", columns={"artist_id"})})
 * @ORM\Entity
 */
class Tracks
{
    /**
     * @var integer
     *
     * @ORM\Column(name="track_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $trackId;

    /**
     * @var integer
     *
     * @ORM\Column(name="artist_id", type="integer", nullable=false)
     */
    private $artistId;

    /**
     * @var string
     *
     * @ORM\Column(name="track_name", type="string", length=200, nullable=false)
     */
    private $trackName;

    /**
     * @var string
     *
     * @ORM\Column(name="artist_name", type="string", length=200, nullable=false)
     */
    private $artistName;

    /**
     * @var string
     *
     * @ORM\Column(name="img_base", type="string", length=500, nullable=false)
     */
    private $imgBase;


}

