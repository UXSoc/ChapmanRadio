<?php

namespace HybridAuthBundle\DependencyInjection;

use HybridAuthBundle\Configuration\HybridAuthConfig;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 5:36 PM.
 */
class HybridAuthExtension extends Extension
{
    const KEYS = [
        'facebook'               => \Hybridauth\Provider\Facebook::class,
        'github'                 => \Hybridauth\Provider\GitHub::class,
        'twitter'                => \Hybridauth\Provider\Twitter::class,
        'bitBucket'              => \Hybridauth\Provider\BitBucket::class,
        'discord'                => \Hybridauth\Provider\Discord::class,
        'disqus'                 => \Hybridauth\Provider\Disqus::class,
        'dribble'                => \Hybridauth\Provider\Dribbble::class,
        'foursquare'             => \Hybridauth\Provider\Foursquare::class,
        'google'                 => \Hybridauth\Provider\Google::class,
        'instagram'              => \Hybridauth\Provider\Instagram::class,
        'linkedin'               => \Hybridauth\Provider\LinkedIn::class,
        'odnoklassniki'          => \Hybridauth\Provider\Odnoklassniki::class,
        'open_id'                => \Hybridauth\Provider\OpenID::class,
        'paypal_option_id'       => \Hybridauth\Provider\PaypalOpenID::class,
        'reddit'                 => \Hybridauth\Provider\Reddit::class,
        'stack_exchange'         => \Hybridauth\Provider\StackExchange::class,
        'stack_exchange_open_id' => \Hybridauth\Provider\StackExchangeOpenID::class,
        'steam'                  => \Hybridauth\Provider\Steam::class,
        'tumblr'                 => \Hybridauth\Provider\Tumblr::class,
        'twitch_tv'              => \Hybridauth\Provider\TwitchTV::class,
        'vkontakte'              => \Hybridauth\Provider\Vkontakte::class,
        'windows_live'           => \Hybridauth\Provider\WindowsLive::class,
        'wordpress'              => \Hybridauth\Provider\WordPress::class,
        'yahoo'                  => \Hybridauth\Provider\Yahoo::class,
        'yahoo_open_id'          => \Hybridauth\Provider\YahooOpenID::class,
    ];

    /**
     * Loads a specific configuration.
     *
     * @param array                                                   $configs   An array of configuration values
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, \Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new HybridAuthConfig();
        $config = $processor->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $provider = $container->register('cr.hybrid_auth.'.$key);
            $provider->setClass(self::KEYS[$key]);

            $factory = $provider->setFactory(HybridFactoryStaticFactory::class.'::createHybridFactory');
            $factory->setArguments([$key, $value]);
        }
    }
}
