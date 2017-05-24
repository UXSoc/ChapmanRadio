<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;

class DjRepository extends EntityRepository
{

    public  function findDjLike($name)
    {
        $dj = $this->createQueryBuilder('dj')
            ->innerJoin('CoreBundle:User','u','WITH','u.id = dj.user_id')
            ->where('u.name LIKE :name')
            ->setParameter("name", '%' . $name . '%');
        return $dj;
    }
}