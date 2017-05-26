<?php
namespace CoreBundle\Filter;

use CoreBundle\Repository\BlogRepository;

class BlogFilter
{
    /** @var \Doctrine\ORM\QueryBuilder  */
    private  $qb;

    private $alias;

    /**
     * BlogFilter constructor.
     * @param BlogRepository $blogRepository
     */
    function __construct($qb,$alias)
    {
        $this->alias = $alias;
        $this->qb = $qb;
    }

    public  function nameIsLike($name)
    {
        $this->qb->where($this->qb->expr()->like($this->alias. '.name',':name'))
            ->setParameter('name','%'.$name.'%');
    }

    public  function query()
    {
        return $this->qb->getQuery();
    }
}