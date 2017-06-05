<?php
namespace CoreBundle\Service;


use CoreBundle\Entity\User;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class UserTokenService
{
    const RESET_TOKEN = "RESET_PASSWORD_";
    const CONFIRMATION_TOKEN = "CONFIRMATION_TOKEN_";

    /**
     * @var AdapterInterface
     */
    private $cacheService;

    /**
     * UserTokenService constructor.
     * @param $cacheService
     */
    public function __construct(CacheItemPoolInterface  $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @param string $token
     * @param User $user
     */
    public function bindPasswordResetToken($token,User $user)
    {
        $cacheItem = $this->cacheService->getItem(UserTokenService::RESET_TOKEN .  $token);
        $cacheItem->expiresAfter(1000);
        $cacheItem->set($user);
        $this->cacheService->save($cacheItem);
    }

    /**
     * @param string $token
     * @return bool|mixed
     */
    public function verifyPasswordResetToken($token)
    {
        if($this->cacheService->hasItem(UserTokenService::RESET_TOKEN .  $token)) {
            $cacheItem = $this->cacheService->getItem(UserTokenService::RESET_TOKEN . $token);
            return $cacheItem->get();
        }
        return false;
    }

    /**
     * @param string $token
     * @param User $user
     */
    public function bindConfirmationToken($token,User $user)
    {
        $cacheItem = $this->cacheService->getItem(UserTokenService::CONFIRMATION_TOKEN .  $token);
        $cacheItem->expiresAfter(1000);
        $cacheItem->set($user);
        $this->cacheService->save($cacheItem);
    }

    /**
     * @param string $token
     * @return bool|mixed
     */
    public function verifyConfirmationToken($token)
    {
        if($this->cacheService->hasItem(UserTokenService::CONFIRMATION_TOKEN .  $token)) {
            $cacheItem = $this->cacheService->getItem(UserTokenService::CONFIRMATION_TOKEN . $token);
            return $cacheItem->get();
        }
        return false;
    }

}
