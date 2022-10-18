<?php declare(strict_types=1);

namespace Mothership\HeadlessShopwareVarnishCacheBundle;

use Mothership\HeadlessShopwareVarnishCacheBundle\DependencyInjection\HeadlessShopwareVarnishCacheExtension;
use Shopware\Core\Framework\Bundle;

class HeadlessShopwareVarnishCacheBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new HeadlessShopwareVarnishCacheExtension();
    }
}
