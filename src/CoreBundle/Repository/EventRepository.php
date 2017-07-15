<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/31/17
 * Time: 10:27 PM
 */

namespace CoreBundle\Repository;


use Carbon\Carbon;
use CoreBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    /**
     * @param \DateTime $dateTime
     * @return Event[]
     */
    public function getEventByDateTime(\DateTime $dateTime)
    {
        $qb = $this->createQueryBuilder('e');
            $qb->where($qb->expr()->eq('e.current',':current'))
                ->setParameter('current',$dateTime);

            $qb->andWhere($qb ->expr()->lt('e.startTime',':time'))
                ->andWhere($qb->expr()->gt('e.endTime',':time'))
                ->setParameter('time',$dateTime);
        return $qb->getQuery()->getResult();
    }

}
