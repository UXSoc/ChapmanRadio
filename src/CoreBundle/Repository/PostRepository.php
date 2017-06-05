<?php
namespace CoreBundle\Repository;


use CoreBundle\Entity\Post;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\HttpFoundation\Request;

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
