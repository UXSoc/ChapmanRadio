<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/18/17
 * Time: 2:27 PM
 */

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