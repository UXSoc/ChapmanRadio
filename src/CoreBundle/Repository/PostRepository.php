<?php
namespace CoreBundle\Repository;


use CoreBundle\Entity\Post;
use CoreBundle\Helper\Datatable;
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
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleResult();
    }

    public function getPostByTokenAndSlug($token, $slug)
    {
        return $this->findOneBy(["token" => $token, "slug" => $slug]);
    }

    private function _filter(Request $request)
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
        return $qb;
    }

    public function filter(Request $request)
    {
        return $this->_filter($request)->getQuery();
    }

    public function dataTableFilter(Request $request)
    {
        $qb = $this->_filter($request);
        $dataTable = new Datatable();
        $dataTable->handleSort($request,['name','created_at','updated_at','status','author']);
        foreach ($dataTable->getSort() as $key => $value)
        {
            switch ($key)
            {
                case 'author':
                    $qb->join('s.author','a','WITH');
                    $qb->orderBy('a.name', $value);
                    break;
                default:
                    $qb->orderBy('s.' . $key,$value);
                    break;
            }
        }
        $paginator = $this->paginator($qb->getQuery(),
            (int)$request->get('page',0),
            (int)$request->get('entries',10),20);

        $dataTable->setPayload($paginator);
        return $dataTable;
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
