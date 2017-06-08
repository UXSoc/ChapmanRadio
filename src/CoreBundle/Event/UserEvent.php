<?php

namespace CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/4/17
 * Time: 9:25 AM.
 */
class UserEvent extends Event
{
    private $user;

    public function __construct(\CoreBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
