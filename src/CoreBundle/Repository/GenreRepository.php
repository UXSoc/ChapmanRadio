<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 5:25 PM
 */

namespace CoreBundle\Repository;


use CoreBundle\Entity\Genre;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class GenreRepository extends EntityRepository
{
    /**
     * @param string $tag
     */
    public function getOrCreateGenre($genre)
    {

        $em = $this->getEntityManager();

        $result = null;
        $qb =  $this->createQueryBuilder('t');
        try {
            $result = $qb->where($qb->expr()->eq("genre", ":genre"))
                ->setParameter("genre", $genre)
                ->getQuery()
                ->getSingleResult();
        }
        catch (NoResultException $e)
        {
            $result = new Genre();
            $result->setGenre($genre);

            $em->persist($result);
            $em->flush();
        }
        return $result;
    }

    /**
     * @param $genre
     * @param int $limit
     * @return array
     */
    public function findGenre($genre,$limit = -1)
    {
        $qb = $this->createQueryBuilder("t");
        $categories = $qb->where($qb->expr()->like('t.genre',':genre'))
            ->setParameter("genre",'%'. $genre.'%')
            ->getQuery();
        if($limit > 0)
            $categories->setMaxResults($limit);
        return $categories->getResult();
    }

    public function getGenre($genre)
    {
        return $this->findOneBy(["genre" => $genre]);
    }
}
