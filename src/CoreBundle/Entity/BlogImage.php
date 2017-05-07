<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlogImage
 *
 * @ORM\Table(name="blog_image", indexes={@ORM\Index(name="blog_image_blog_id_fk", columns={"blog_id"}), @ORM\Index(name="blog_image_image_id_fk", columns={"image_id"})})
 * @ORM\Entity
 */
class BlogImage
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
     * @var \Blog
     *
     * @ORM\ManyToOne(targetEntity="Blog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="blog_id", referencedColumnName="id")
     * })
     */
    private $blog;

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

