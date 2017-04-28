<?php namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evals
 *
 * @ORM\Table(name="evals")
 * @ORM\Entity
 */
class Evals
{
    /**
     * @var integer
     *
     * @ORM\Column(name="evalid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $evalid;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="bigint", nullable=false)
     */
    private $userid;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="bigint", nullable=false)
     */
    private $showid;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="postedtimestamp", type="bigint", nullable=false)
     */
    private $postedtimestamp;

    /**
     * @var boolean
     *
     * @ORM\Column(name="live", type="boolean", nullable=false)
     */
    private $live = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="goodbad", type="string", nullable=false)
     */
    private $goodbad;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=400, nullable=false)
     */
    private $value;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=10, nullable=false)
     */
    private $season;


}

