<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/31/17
 * Time: 10:27 PM.
 */

namespace CoreBundle\Repository;

use CoreBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    /**
     * @param \DateTime $time
     *
     * @return Event[]
     */
    public function getEventByTime(\DateTime $time)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where($qb->expr()->lt('e.start', ':time'))
                ->where($qb->expr()->gt('e.end', ':time'))
                ->setParameter('time', $time);

        return $qb->getQuery()->getResult();
    }
}
