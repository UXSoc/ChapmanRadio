<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/31/17
 * Time: 10:27 PM
 */

namespace CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    public function getCurrentActiveEvent()
    {
        $qb = $this->createQueryBuilder('e');
            $qb->where($qb ->expr()->gt('e.start',':start'))
                ->where($qb->expr()->lt('e.end',':end'));
        return $qb->getQuery()->getResult();
    }

}