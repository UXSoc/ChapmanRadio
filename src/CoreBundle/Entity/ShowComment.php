<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowComment
 *
 * @ORM\Table(name="show_comment", indexes={@ORM\Index(name="show_comment_comment_id_fk", columns={"comment_id"}), @ORM\Index(name="show_comment_show_id_fk", columns={"show_id"})})
 * @ORM\Entity
 */
class ShowComment
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
     * @var \Comment
     *
     * @ORM\ManyToOne(targetEntity="Comment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     * })
     */
    private $comment;

    /**
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id")
     * })
     */
    private $show;


}

