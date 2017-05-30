<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/29/17
 * Time: 10:37 PM
 */

namespace CoreBundle\Repository;

use CoreBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class TagRepository  extends EntityRepository
{
    /**
     * @param string $tag
     */
    public function findOrCreateTag($tag)
    {
        $em = $this->getEntityManager();

        $result = null;
        $qb =  $this->createQueryBuilder('t');
        try {
            $result = $qb->where($qb->expr()->eq("tag", ":tag"))
                ->setParameter("tag", $tag)
                ->getQuery()
                ->getSingleResult();
        }
        catch (NoResultException $e)
        {
            $result = new Tag();
            $result->setTag($tag);

            $em->persist($result);
            $em->flush();
        }
        return $result;
    }

    public function findTag($tag)
    {
        return $this->findOneBy(["tag" => $tag]);
    }
}