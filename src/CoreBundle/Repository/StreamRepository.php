<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/2/17
 * Time: 11:29 PM
 */

namespace CoreBundle\Repository;


use CoreBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;

class StreamRepository extends EntityRepository
{
    /**
     * @param Event $event
     */
    public function getStreamesTiedToEvent($event)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->where($qb->expr()->eq('s.event',':event'))
            ->setParameter('event',$event);
        return $qb->getQuery()->getResult();
    }

    public function getStreamsNotTiedToEvent()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->where($qb->expr()->isNull('s.event'));
        return $qb->getQuery()->getResult();
    }

    public function getStreamsTiedToEvent()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->where($qb->expr()->isNotNull('s.event'));
        return $qb->getQuery()->getResult();
    }
}
