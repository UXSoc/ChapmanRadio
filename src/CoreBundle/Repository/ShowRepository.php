<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved

namespace CoreBundle\Repository;


use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Show;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class ShowRepository extends EntityRepository
{
    public function getPostByTokenAndSlug($token,$slug)
    {
        return $this->findOneBy(["token" => $token,"slug" => $slug]);
    }


    public function filter(Request $request)
    {
        $qb = $this->createQueryBuilder('s');
        if($name = $request->get('name',null))
        {
            $qb->where($qb->expr()->like('name',':name'))
                ->setParameter('name','%' .$name.'%');
        }

        return $qb->getQuery();
    }

    /**
     * @param Query $query
     * @param $page
     * @param $perPage
     * @param int $limit
     * @return Paginator
     */
    public function  paginator(Query $query,$page,$perPage,$limit = 10)
    {

        $pagination = new Paginator($query);
        $num = $perPage > $limit ? $perPage :  $limit;
        $pagination->getQuery()->setMaxResults($num);
        $pagination->getQuery()->setFirstResult($num * $page);
        return $pagination;
    }

}
