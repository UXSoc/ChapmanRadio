<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowUser
 *
 * @ORM\Table(name="show_dj")
 * @ORM\Entity
 */
class ShowDj
{

    /**
     *
     */
    private $permissions;

    /**
     * @var Show
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Show",inversedBy="showDjs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id")
     * })
     */
    private $show;

    /**
     * @var Dj
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Dj",inversedBy="showDj")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dj_id", referencedColumnName="id")
     * })
     */
    private $dj;


}

