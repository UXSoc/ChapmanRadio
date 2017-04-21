<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Features
 *
 * @ORM\Table(name="features")
 * @ORM\Entity
 */
class Features
{
    /**
     * @var integer
     *
     * @ORM\Column(name="feature_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $featureId;

    /**
     * @var string
     *
     * @ORM\Column(name="feature_type", type="string", nullable=false)
     */
    private $featureType;

    /**
     * @var string
     *
     * @ORM\Column(name="feature_title", type="string", length=400, nullable=true)
     */
    private $featureTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="feature_link", type="string", length=400, nullable=true)
     */
    private $featureLink;

    /**
     * @var string
     *
     * @ORM\Column(name="feature_text", type="text", nullable=false)
     */
    private $featureText;

    /**
     * @var integer
     *
     * @ORM\Column(name="feature_priority", type="bigint", nullable=false)
     */
    private $featurePriority = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="feature_active", type="boolean", nullable=false)
     */
    private $featureActive = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="feature_size", type="integer", nullable=false)
     */
    private $featureSize;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="feature_posted", type="datetime", nullable=false)
     */
    private $featurePosted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="feature_expires", type="datetime", nullable=false)
     */
    private $featureExpires;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionkey", type="string", length=30, nullable=false)
     */
    private $revisionkey = '';


}

