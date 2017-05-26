<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/25/17
 * Time: 10:17 AM
 */

namespace CoreBundle\Helper;


use Doctrine\ORM\Query;

class Paginator extends  \Doctrine\ORM\Tools\Pagination\Paginator
{
    private $currentPage = 0;

    private  $entriesPerPage = 10;

    public function __construct($query, $fetchJoinCollection = true)
    {
        parent::__construct($query, $fetchJoinCollection);
    }

    public  function setCurrentPage($page){
        $this->currentPage = $page;
    }

    public  function setEntriesPerPage($entries,$limit = 10)
    {
        if($entries > $limit )
            $entries = $limit;
        $this->entriesPerPage = $entries;
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function asRestfulResponse($callback){
        /** @var Query  $query */
        $query = $this->getQuery();
        $query->setMaxResults($this->entriesPerPage);
        $query->setFirstResult($this->entriesPerPage * $this->currentPage);
        return [
            "count" => $this->count(),
            "perPage" => $this->entriesPerPage,
            "pages" => ceil( $this->count()/$this->entriesPerPage),
            "result" => $callback($query)
        ];
    }

}