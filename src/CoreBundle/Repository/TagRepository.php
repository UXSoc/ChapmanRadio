<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/29/17
 * Time: 10:37 PM.
 */

namespace CoreBundle\Repository;

use CoreBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class TagRepository extends EntityRepository
{
    /**
     * @param string $tag
     */
    public function getOrCreateTag($tag)
    {
        $em = $this->getEntityManager();

        $result = null;
        $qb = $this->createQueryBuilder('t');
        try {
            $result = $qb->where($qb->expr()->eq('t.tag', ':tag'))
                ->setParameter('tag', $tag)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $result = new Tag();
            $result->setTag($tag);
        }

        return $result;
    }

    /**
     * @param string $tag
     *
     * @return Tag
     */
    public function getTag($tag)
    {
        return $this->findOneBy(['tag' => $tag]);
    }

    public function filter(Request $request)
    {
        $qb = $this->createQueryBuilder('t');

        if ($name = $request->get('name', null)) {
            $qb->where($qb->expr()->like('t.tag', ':tag'))
                ->setParameter('tag', '%'.$name.'%');
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
