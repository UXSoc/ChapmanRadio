<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity
 */
class News
{
    /**
     * @var integer
     *
     * @ORM\Column(name="news_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $newsId;

    /**
     * @var string
     *
     * @ORM\Column(name="news_title", type="string", length=255, nullable=false)
     */
    private $newsTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="news_body", type="text", nullable=false)
     */
    private $newsBody;

    /**
     * @var integer
     *
     * @ORM\Column(name="news_postedby", type="bigint", nullable=false)
     */
    private $newsPostedby;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="news_posted", type="datetime", nullable=true)
     */
    private $newsPosted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="news_expires", type="datetime", nullable=true)
     */
    private $newsExpires;


}

