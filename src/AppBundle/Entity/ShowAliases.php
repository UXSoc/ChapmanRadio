<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowAliases
 *
 * @ORM\Table(name="show_aliases")
 * @ORM\Entity
 */
class ShowAliases
{
    /**
     * @var integer
     *
     * @ORM\Column(name="from_show_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $fromShowId;

    /**
     * @var integer
     *
     * @ORM\Column(name="to_show_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $toShowId;


}

