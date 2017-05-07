<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blog
 *
 * @ORM\Table(name="blog", indexes={@ORM\Index(name="blog_user_id_fk", columns={"author_id"})})
 * @ORM\Entity
 */
class Blog
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=60, nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="post_exceprt", type="text", length=65535, nullable=true)
     */
    private $postExceprt;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=40, nullable=true)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_pinned", type="boolean", nullable=true)
     */
    private $isPinned;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="blob", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * })
     */
    private $author;


}

