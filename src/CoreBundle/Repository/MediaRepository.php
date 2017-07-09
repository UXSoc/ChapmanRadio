<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 6:57 PM
 */

namespace CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class MediaRepository extends EntityRepository
{
    public function getMediaByToken($token)
    {
        return $this->findOneBy(["token" => $token]);
    }

    public function _filter(Request $request)
    {
        $qb = $this->createQueryBuilder('m');
        if($isHidden = $request->get('is_hidden',null))
        {
            $qb->where($qb->expr()->eq('isHidden',':isHidden'))
                ->setParameter('isHidden',$isHidden);
        }

        if($name = $request->get('name' ,null))
        {
            $qb->where($qb->expr()->like('name',':name'))
                ->setParameter('name','%' .$name .'%');
        }
        return $qb;
    }

    public function filter(Request $request)
    {
        return $this->_filter($request)->getQuery();
    }

    /**
     * @param Query $query
     * @param int $page
     * @param int $perPage
     * @param int $limit
     * @return Paginator
     */
    public function  paginator(Query $query,$page,$perPage,$limit = 10)
    {
        $pagination = new Paginator($query);
        $num = $perPage < $limit ? $perPage :  $limit;
        $pagination->getQuery()->setMaxResults($num);
        $pagination->getQuery()->setFirstResult($num * $page);
        return $pagination;
    }

}