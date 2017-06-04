<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 7:48 PM
 */

namespace HybridAuthBundle;


use Hybridauth\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class HybridAuthStorage implements StorageInterface
{
    /**
     * Namespace
     *
     * @var string
     */
    protected $storeNamespace = 'HYBRIDAUTH::STORAGE';

    private $session;

    function __construct()
    {
        $this->session = new Session();

//        $this->session->start();
    }

    /**
     * Retrieve a item from storage
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $this->session->get($key);
    }



    /**
     * Add or Update an item to storage
     *
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        return $this->session->get($key,$value);
    }

    /**
     * Delete an item from storage
     *
     * @param string $key
     */
    public function delete($key)
    {
       return $this->session->remove($key);
    }

    /**
     * Delete a item from storage
     *
     * @param string $key
     *
     */
    public function deleteMatch($key)
    {
        /** @var \ArrayIterator $itr */
        foreach ($this->session->getIterator() as $itr)
        {
            if(strstr($itr->key(),$key))
            {
                $this->session->remove($itr->key());
            }
        }
    }

    /**
     * Clear all items in storage
     */
    public function clear()
    {
        $this->session->clear();
    }
}