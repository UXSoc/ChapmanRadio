<?php
namespace CoreBundle\DependencyInjection;
use CoreBundle\Event\AuthSubscriber;
use CoreBundle\Service\UserTokenService;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/4/17
 * Time: 10:50 AM
 */
class CoreExtension  extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $container->addCompilerPass(new RegisterListenersPass());

        $container->register(AuthSubscriber::class,\CoreBundle\Event\AuthSubscriber::class)
            ->setArguments([new Reference('mailer'),new Reference('twig'),new Reference('logger'),new Reference(UserTokenService::class)])
            ->addTag('kernel.event_subscriber');
    }
}