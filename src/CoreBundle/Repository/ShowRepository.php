<?php

// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved

namespace CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class ShowRepository extends EntityRepository
{
    public function getShowByTokenAndSlug($token, $slug)
    {
        return $this->findOneBy(['token' => $token, 'slug' => $slug]);
    }

    public function filter(Request $request)
    {
        $qb = $this->createQueryBuilder('s');
        if ($name = $request->get('name', null)) {
            $qb->where($qb->expr()->like('name', ':name'))
                ->setParameter('name', '%'.$name.'%');
        }

        if ($genres = $request->get('genre', null)) {
            $qb->join('s.genres', 'g', 'WITH');
            if (!is_array($genres)) {
                $genres = [$genres];
            }

            foreach ($genres as $genre) {
                $qb->where($qb->expr()->eq('g.genre', ':genre'))
                    ->setParameter('genre', $genre);
            }
        }

        if ($tags = $request->get('tag', null)) {
            $qb->join('s.tags', 't', 'WITH');
            if (!is_array($tags)) {
                $tags = [$tags];
            }

            foreach ($tags as $tag) {
                $qb->where($qb->expr()->eq('t.tag', ':tag'))
                    ->setParameter('tag', $tag);
            }
        }

        return $qb->getQuery();
    }

    /**
     * @param Query $query
     * @param $page
     * @param $perPage
     * @param int $limit
     *
     * @return Paginator
     */
    public function paginator(Query $query, $page, $perPage, $limit = 10)
    {
        $pagination = new Paginator($query);
        $num = $perPage > $limit ? $perPage : $limit;
        $pagination->getQuery()->setMaxResults($num);
        $pagination->getQuery()->setFirstResult($num * $page);

        return $pagination;
    }
}
