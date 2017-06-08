<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/6/17
 * Time: 10:00 PM
 */

namespace CoreBundle\Repository;


use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository
{


    public function getByDatetime(Carbon $dateTime,$archive = null,$profanity = null)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->where($qb->expr()->lte('s.startDate',':start'))
            ->setParameter('start',$dateTime->startOfDay());

        $qb->andWhere($qb->expr()->orX( $qb->expr()->gte('s.endDate',':end'),$qb->expr()->isNull('s.endDate')))
            ->setParameter('end',$dateTime->startOfDay());

        if(!is_null($archive))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.archive',$archive));
        if(!is_null($profanity))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.profanity',$profanity));

        return $qb->getQuery()->getResult();
    }

}