<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 6:57 PM
 */

namespace CoreBundle\Repository;


use CoreBundle\Entity\Media;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Show;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
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
     * @param Show $show
     * @param string $token
     * @return Media | null
     *
     */
    public function getMediaByShowAndToken(Media $show,  $token)
    {
        $qb = $this->createQueryBuilder('c');
        try {
            $result = $qb->join('c.show', 's', 'WITH', $qb->expr()->eq('s.id', ':id'))
                ->setParameter('id', $show->getId())
                ->where($qb->expr()->eq('c.token', ':token'))
                ->setParameter('token', $token)
                ->getQuery()
                ->getSingleResult();
            return $result;
        }
        catch (NoResultException $e)
        {
            return null;
        }
    }


    /**
     * @param Post $post
     * @param string $token
     * @return Media | null
     */
    public function getMediaByPostAndToken(Media $post, $token)
    {
        $qb = $this->createQueryBuilder('c');
        try {

            $result = $qb->join('c.post', 'p', 'WITH', $qb->expr()->eq('p.id', ':id'))
                ->setParameter('id', $post->getId())
                ->where($qb->expr()->eq('c.token', ':token'))
                ->setParameter('token', $token)
                ->getQuery()
                ->getSingleResult();
            return $result;
        }
        catch (NoResultException $e)
        {
            return null;
        }

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