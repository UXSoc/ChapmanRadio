<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/20/17
 * Time: 5:15 PM
 */

namespace CoreBundle\Helper;


use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class Datatable
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    private  $columnSort = [];

    private $payload;

    function __construct()
    {
    }

    public function handleSort(Request $request,$sortColumns = [])
    {
        $sort = $request->get('sort',[]);
        foreach ($sortColumns as $column)
        {
            if(array_key_exists($column,$sort))
            {
                switch ($sort[$column])
                {
                    case 'ASC':
                        $this->columnSort[$column] = Datatable::ASC;
                        break;
                    case 'DESC':
                        $this->columnSort[$column] = Datatable::DESC;
                        break;
                }
            }
        }
    }

    public function getSort()
    {
        return $this->columnSort;
    }


    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    public function getPayload()
    {
        return $this->payload;
    }


}