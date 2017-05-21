<?php
namespace CoreBundle\Helper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Traversable;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/20/17
 * Time: 6:51 PM
 */
class DataTable  implements \Countable, \IteratorAggregate
{

    /** @var QueryBuilder */
    private $query;
    private $columnSort = array();
    private $perPage = 10;
    private $currentPage = 0;
    private $columnAlias = [];
    private $count;
    private $index;

    /**
     * DataTable constructor.
     * @param QueryBuilder $repository
     */
    public function __construct($queryBuilder){
        $this->query = $queryBuilder;
    }

    public  function setAlias($alias){
        $this->columnAlias = $alias;
        return $this;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public  function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param $page
     * @return DataTable $this
     */
    public function withCurrentPage($page)
    {
        $this->currentPage = $page;
        return $this;
    }

    public  function withPerPage($count)
    {
        $this->perPage = $count;
        return $this;
    }

    public function setSort($column,$sort)
    {
        $this->columnSort[$column] = $sort;
        return $this;
    }

    public  function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }


    public function parseSort($columns,$input)
    {
        foreach ($input as $key => $value)
        {
            if(in_array($key,$columns))
            {
                switch ($value)
                {
                    case 'asc':
                        $this->columnSort[$key] = 'asc';
                        break;
                    case 'desc':
                        $this->columnSort[$key] = 'desc';
                        break;
                }

            }
        }
        return $this;
    }


    public  function getQueryBuilder()
    {
        $query = clone $this->query;

        foreach ($this->columnSort as $key => $value) {
            if(array_key_exists($key,$this->columnAlias)) {
                $query->addOrderBy($this->columnAlias[$key], $value);
            }
        }
        $query->setMaxResults($this->perPage);
        $query->setFirstResult($this->perPage * $this->currentPage);

        return $query;
    }

    public function count()
    {

        if ($this->count === null) {
            try {
                $countQuery =  $this->getQueryBuilder();

                $countQuery->setFirstResult(null)->setMaxResults(null);
                $countQuery->select('count('.$this->index.')');

                $this->count = (int)$countQuery->getQuery()->getSingleScalarResult();
            } catch(NoResultException $e) {
                $this->count = 0;
            }
        }
        return $this->count;
    }



    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getQueryBuilder()->getQuery()->getResult());
    }




}