<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DjImage
 *
 * @ORM\Table(name="dj_image", indexes={@ORM\Index(name="dj_image_dj_id_fk", columns={"dj_id"}), @ORM\Index(name="dj_image_image_id_fk", columns={"image_id"})})
 * @ORM\Entity
 */
class DjImage
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
     * @var \Dj
     *
     * @ORM\ManyToOne(targetEntity="Dj")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dj_id", referencedColumnName="id")
     * })
     */
    private $dj;

    /**
     * @var \Image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;


}

