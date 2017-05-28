<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/28/17
 * Time: 12:49 AM
 */

namespace CoreBundle\Service;

//http://symfony.com/doc/current/bundles/DoctrineCacheBundle/reference.html
use CoreBundle\Entity\User;
use Doctrine\Common\Cache\CacheProvider;
use Keygen\Keygen;

class UserService
{
    private  $cache;

    /**
     * UserService constructor.
     * @param CacheProvider $cache
     */
    function __construct($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param User $user
     */
    public function createConfirmationToken($user)
    {
        $token = Keygen::alphanum(20)->generate();
        $this->cache->setNamespace('user_keys_confirm');
        $this->cache->save($token,$user->getToken(),1000);
        return $token;
    }

    public function verifyConfirmationToken($token)
    {
        $this->cache->setNamespace('user_keys_confirm');
        $result = $this->cache->fetch($token);
        if($result)
        {
            $this->cache->delete($token);
            return $result;
        }
        return null;
    }



}