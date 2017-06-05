<?php
namespace CoreBundle\Repository;


use CoreBundle\Entity\Post;
use Doctrine\ORM\EntityRepository;
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

    public function getPostByTokenAndSlug($token,$slug)
    {
        return $this->findOneBy(["token" => $token,"slug" => $slug]);
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
