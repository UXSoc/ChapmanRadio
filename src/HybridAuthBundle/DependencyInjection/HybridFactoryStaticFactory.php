<?php
namespace HybridAuthBundle\DependencyInjection;

use HybridAuthBundle\DependencyInjection\HybridAuthExtension;
use HybridAuthBundle\HybridAuthStorage;
use ReflectionClass;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 6:13 PM
 */
class HybridFactoryStaticFactory
{
    /**
     * @param $type
     * @param $config
     */
    public static function createHybridFactory($type,$config)
    {
        $reflection = new ReflectionClass(HybridAuthExtension::KEYS[$type]);
        /** @var AdapterInterface $hybridAuth */
        $hybridAuth = $reflection->newInstance($config,null,new HybridAuthStorage());

        return $hybridAuth ;
    }
}
