<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/25/17
 * Time: 12:52 AM
 */

namespace CoreBundle\Repository;


use CoreBundle\Entity\Post;
use CoreBundle\Entity\Comment;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\Expr;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;

class PostRepository extends EntityRepository
{


    public function findPostByName($name)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name = :name')
            ->setParameter('name',$name)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param Post $post
     * @param  $tag
     */
    public function getPostsByTag($post,$tag)
    {

    }

    /**
     * @param Post $post
     * @param Category $category
     */
    public  function getPostsByCategory( $post, $category)
    {
    }

}