<?php declare(strict_types=1);

namespace Mothership\HeadlessShopwareVarnishCacheBundle\Event;

use Mothership\HeadlessShopwareVarnishCacheBundle\Cache\GatewayInterface;
use Shopware\Core\Framework\Adapter\Cache\InvalidateCacheEvent;
use Shopware\Storefront\Framework\Routing\RequestTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;

/**
 * @template TCachedContent
 */
class ReverseProxyCache implements StoreInterface
{
    private GatewayInterface $gateway;

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function __invoke(InvalidateCacheEvent $event): void
    {
        $this->gateway->invalidate($event->getKeys());
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function lookup(Request $request): ?Response
    {
        return null;
    }

    public function write(Request $request, Response $response): string
    {
        return '';
    }

    public function invalidate(Request $request): void
    {
        $uri = $request->attributes->get(RequestTransformer::ORIGINAL_REQUEST_URI);

        if ($uri === null) {
            return;
        }

        $this->gateway->purge([$uri]);
    }

    /**
     * This should be done in reverse cache
     */
    public function lock(Request $request): bool
    {
        return true;
    }

    /**
     * This should be done in reverse cache
     */
    public function unlock(Request $request): bool
    {
        return true;
    }

    /**
     * This should be done in reverse cache
     */
    public function isLocked(Request $request): bool
    {
        return false;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function purge(string $url): bool
    {
        $this->gateway->purge([$url]);

        return true;
    }

    /**
     * We don't need cleanup
     */
    public function cleanup(): void
    {
    }
}
