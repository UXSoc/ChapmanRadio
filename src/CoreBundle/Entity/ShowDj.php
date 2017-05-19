<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowUser
 *
 * @ORM\Table(name="show_dj", indexes={@ORM\Index(name="show_dj_show_id_fk", columns={"show_id"}), @ORM\Index(name="show_dj_id_fk", columns={"dj_id"})})
 * @ORM\Entity
 */
class ShowDj
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id")
     * })
     */
    private $show;

    /**
     * @var \Dj
     *
     * @ORM\ManyToOne(targetEntity="Dj")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dj_id", referencedColumnName="id")
     * })
     */
    private $dj;


}

