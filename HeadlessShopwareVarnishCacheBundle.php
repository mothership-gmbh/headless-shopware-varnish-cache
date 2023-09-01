<?php declare(strict_types=1);

namespace Mothership\HeadlessShopwareVarnishCacheBundle;

use Mothership\HeadlessShopwareVarnishCacheBundle\DependencyInjection\HeadlessShopwareVarnishCacheExtension;
use Shopware\Core\Framework\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class HeadlessShopwareVarnishCacheBundle extends Bundle
{
    /**
     * @return ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new HeadlessShopwareVarnishCacheExtension();
    }
}
