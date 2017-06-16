<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/15/17
 * Time: 11:04 PM
 */

namespace CoreBundle\Helper;


use RRule\RRule;

class CacheableRule extends RRule
{

    private $restoredTotal;

    public function getCache()
    {
        return [
            "cache" => $this->cache,
            "total" => $this->total
        ];
    }


    public function restoreFromCache($cache)
    {
        $this->cache = $cache["cache"];
        $this->total = $cache["total"];
        $this->restoredTotal = $this->total;
    }

    public function isCacheExausted()
    {
        return ($this->restoredTotal !== $this->total);
    }

    public function isCacheUsed()
    {
        return $this->total !== null && $this->total !== 0;
    }

}