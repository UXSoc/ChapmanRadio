<?php
namespace HybridAuthBundle\Configuration;

use HybridAuthBundle\DependencyInjection\HybridAuthExtension;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 5:07 PM
 */
class HybridAuthConfig implements  ConfigurationInterface
{


    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hybrid_auth');
        foreach (HybridAuthExtension::KEYS as $key => $value) {
            $rootNode->children()->arrayNode($key)
                ->children()
                        ->scalarNode('callback')->isRequired()->cannotBeEmpty()->end()
                        ->arrayNode('keys')
                            ->children()
                                ->scalarNode('key')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->scalarNode('scope')->end()
                    ->end()
                ->end();
        }


        return $treeBuilder;
    }
}
