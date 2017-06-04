<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved

namespace CoreBundle\Repository;


use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Show;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class ShowRepository extends EntityRepository
{
    public function getPostByTokenAndSlug($token,$slug)
    {
        return $this->findOneBy(["token" => $token,"slug" => $slug]);
    }
}
