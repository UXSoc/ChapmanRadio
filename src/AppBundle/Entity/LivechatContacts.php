<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LivechatContacts
 *
 * @ORM\Table(name="livechat_contacts")
 * @ORM\Entity
 */
class LivechatContacts
{
    /**
     * @var string
     *
     * @ORM\Column(name="contactkey", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $contactkey;

    /**
     * @var string
     *
     * @ORM\Column(name="contactname", type="string", length=200, nullable=false)
     */
    private $contactname;

    /**
     * @var string
     *
     * @ORM\Column(name="contactip", type="string", length=255, nullable=false)
     */
    private $contactip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="contactupdated", type="datetime", nullable=false)
     */
    private $contactupdated = 'CURRENT_TIMESTAMP';


}

