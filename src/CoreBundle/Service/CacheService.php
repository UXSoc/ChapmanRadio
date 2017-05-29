<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/28/17
 * Time: 9:54 AM
 */

namespace CoreBundle\Service;


use Doctrine\Common\Cache\CacheProvider;

/**
 * A repository container to wrap Doctrine Cache
 * @package CoreBundle\Service
 */
class CacheService
{
    /** @var CacheProvider  */
    private  $cache ;

    /**
     * UserService constructor.
     * @param CacheProvider $cache
     */
    function __construct($cache)
    {
        $this->cache = $cache;
    }

    /**
    * Sets the namespace to prefix all cache ids with.
    *
    * @param string $namespace
    *
    * @return void
    */
    public function setNamespace($namespace)
    {
        $this->cache->setNamespace($namespace);
    }

    /**
    * Retrieves the namespace that prefixes all cache ids.
    *
    * @return string
    */
    public function getNamespace()
    {
        return $this->getNamespace();
    }

    /**
    * Fetches an entry from the cache.
    *
    * @param string $id The id of the cache entry to fetch.
    *
    * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
    */
    public function fetch($id)
    {
        return $this->cache->fetch($id);
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function contains($id)
    {
        return $this->cache->contains($id);
    }

    /**
     * Puts data into the cache.
     *
     * If a cache entry with the given id already exists, its data will be replaced.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime The lifetime in number of seconds for this cache entry.
     *                         If zero (the default), the entry never expires (although it may be deleted from the cache
     *                         to make place for other entries).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->cache->save($id,$data,$lifeTime );
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful.
     */
    public function delete($id)
    {
        $this->cache->delete($id);
    }

    /**
     * Retrieves cached information from the data store.
     *
     * The server's statistics array has the following values:
     *
     * - <b>hits</b>
     * Number of keys that have been requested and found present.
     *
     * - <b>misses</b>
     * Number of items that have been requested and not found.
     *
     * - <b>uptime</b>
     * Time that the server is running.
     *
     * - <b>memory_usage</b>
     * Memory used by this server to store items.
     *
     * - <b>memory_available</b>
     * Memory allowed to use for storage.
     *
     * @since 2.2
     *
     * @return array|null An associative array with server's statistics if available, NULL otherwise.
     */
    public function getStats()
    {
        return $this->cache->getStats();
    }


    /**
     * Deletes all cache entries in the current cache namespace.
     *
     * @return bool TRUE if the cache entries were successfully deleted, FALSE otherwise.
     */
    public function deleteAll()
    {
        $this->cache->deleteAll();
    }

    /**
     * Flushes all cache entries, globally.
     *
     * @return bool TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    public function flushAll()
    {
        return $this->cache->flushAll();
    }

    /**
     * Returns an associative array of values for keys is found in cache.
     *
     * @param string[] $keys Array of keys to retrieve from cache
     * @return mixed[] Array of retrieved values, indexed by the specified keys.
     *                 Values that couldn't be retrieved are not contained in this array.
     */
    public function fetchMultiple($keys)
    {
        return $this->cache->fetchMultiple($keys);
    }



    /**
     * Returns a boolean value indicating if the operation succeeded.
     *
     * @param array $keysAndValues  Array of keys and values to save in cache
     * @param int   $lifetime       The lifetime. If != 0, sets a specific lifetime for these
     *                              cache entries (0 => infinite lifeTime).
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't.
     */
    public function saveMultiple(array $keysAndValues, $lifetime = 0)
    {
        return $this->cache->saveMultiple($keysAndValues,$lifetime);
    }

}