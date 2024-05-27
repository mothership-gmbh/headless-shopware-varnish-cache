<?php declare(strict_types=1);

namespace Mothership\HeadlessShopwareVarnishCacheBundle\Cache;

use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

class VarnishCacheClearer implements CacheClearerInterface
{
    public function __construct(protected readonly GatewayInterface $gateway)
    {
    }

    public function clear(string $cacheDir): void
    {
        $this->gateway->invalidate(['all']);
    }
}
