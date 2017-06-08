<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/6/17
 * Time: 10:00 PM
 */

namespace CoreBundle\Repository;


use Carbon\Carbon;
use CoreBundle\Entity\Show;
use Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository
{

    private function byDateTime(Carbon $dateTime)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->where($qb->expr()->lte('s.startDate',':start'))
            ->setParameter('start',$dateTime->startOfDay());

        $qb->andWhere($qb->expr()->orX( $qb->expr()->gte('s.endDate',':end'),$qb->expr()->isNull('s.endDate')))
            ->setParameter('end',$dateTime->startOfDay());
        return $qb;
    }

    private function byDateTimeRange(Carbon $start,Carbon $end)
    {
        $qb = $this->createQueryBuilder('s');

        $qb->where($qb->expr()->orX($qb->expr()->lte(':start','s.endDate'),$qb->expr()->isNull('s.endDate')))
            ->setParameter('start',$start->startOfDay());

        $qb->andWhere($qb->expr()->lte('s.startDate',':end'))
            ->setParameter('end',$end->startOfDay());

        $qb->orWhere($qb->expr()->andX($qb->expr()->lte('s.startDate',':start'),$qb->expr()->lte(':end','s.endDate')))
            ->setParameter('start',$start->startOfDay())
            ->setParameter('end',$end->startOfDay());
        return $qb;
    }

    public function getByToken($token)
    {
        return $this->findOneBy(['token' => $token]);
    }


    public function getByDatetime(Carbon $dateTime,$archive = null,$profanity = null)
    {
        $qb = $this->byDateTime($dateTime);

        if(!is_null($archive))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.archive',$archive));
        if(!is_null($profanity))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.profanity',$profanity));

        return $qb->getQuery()->getResult();
    }

    public function getShowByDatetime(Carbon $dateTime,Show $show,$archive = null,$profanity = null)
    {
        $qb = $this->byDateTime($dateTime);

        $qb->andWhere($qb->expr()->eq('s.show',':show'))
            ->setParameter('show',$show);

        if(!is_null($archive))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.archive',$archive));
        if(!is_null($profanity))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.profanity',$profanity));

        return $qb->getQuery()->getResult();
    }

    public function getByDatetimeRange(Carbon $start,Carbon $end,$archive = null,$profanity = null)
    {
        $qb = $this->byDateTimeRange($start,$end);

        if(!is_null($archive))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.archive',$archive));
        if(!is_null($profanity))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.profanity',$profanity));

        return $qb->getQuery()->getResult();
    }

    public function getByShowDatetimeRange(Carbon $start,Carbon $end,Show $show,$archive = null,$profanity = null)
    {
        $qb = $this->byDateTimeRange($start,$end);

        $qb->andWhere($qb->expr()->eq('s.show',':show'))
            ->setParameter('show',$show);

        if(!is_null($archive))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.archive',$archive));
        if(!is_null($profanity))
            $qb->join('s.show','sh','WITH',$qb->expr()->eq('sh.profanity',$profanity));

        return $qb->getQuery()->getResult();
    }

}