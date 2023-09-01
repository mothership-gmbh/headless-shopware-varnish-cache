<?php

namespace Mothership\HeadlessShopwareVarnishCacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('headless_shopware_varnish_cache');
        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
                ?->arrayNode('reverse_proxy')
                    ->children()
                        ->variableNode('hosts')->defaultNull()->end()
                        ?->scalarNode('max_parallel_invalidations')->end()
                        ?->scalarNode('ban_method')->end()
                        ?->scalarNode('tag_flush_threshold')->end()
                        ?->booleanNode('use_xkey')->defaultFalse()->end()
                        ?->scalarNode('xkey_chunksize')->defaultValue(50)->end();
        return $treeBuilder;
    }
}
