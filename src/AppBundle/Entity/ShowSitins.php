<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowSitins
 *
 * @ORM\Table(name="show_sitins")
 * @ORM\Entity
 */
class ShowSitins
{
    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $showid;

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=6, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $season;

    /**
     * @var integer
     *
     * @ORM\Column(name="result", type="integer", nullable=false)
     */
    private $result;


}

