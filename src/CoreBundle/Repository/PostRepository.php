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

        if($tags = $request->get('tag',null))
        {
            $qb->join('s.tags','t',"WITH");
            if(!is_array($tags))
                $tags = array($tags);

            foreach ($tags as $tag)
            {
                $qb->where($qb->expr()->eq('t.tag',':tag'))
                    ->setParameter('tag',$tag);
            }
        }

        if($categories = $request->get('category',null))
        {
            $qb->join('s.categories','c','WITH');
            if(!is_array($categories))
                $categories = array($categories);
            foreach ($categories as $category) {
                $qb->where($qb->expr()->eq('c.category',':category'))
                    ->setParameter('category',$category);
            }
        }

        return $qb->getQuery();
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
