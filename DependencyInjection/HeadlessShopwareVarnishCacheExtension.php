<?php

declare(strict_types=1);

namespace Mothership\HeadlessShopwareVarnishCacheBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use function is_array;

class HeadlessShopwareVarnishCacheExtension extends Extension
{

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $this->addConfig($container, 'headless_shopware_varnish_cache', $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param string $alias
     * @param array $options
     * @return void
     */
    private function addConfig(ContainerBuilder $container, string $alias, array $options): void
    {
        foreach ($options as $key => $option) {
            $container->setParameter($alias . '.' . $key, $option);

            if (is_array($option)) {
                $this->addConfig($container, $alias . '.' . $key, $option);
            }
        }
    }
}
