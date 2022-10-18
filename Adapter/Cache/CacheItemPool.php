<?php

namespace Mothership\HeadlessShopwareVarnishCacheBundle\Adapter\Cache;

use Mothership\HeadlessShopwareVarnishCacheBundle\Cache\VarnishGateway;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPool implements CacheItemPoolInterface
{

    private VarnishGateway $varnishGateway;

    public function __construct(VarnishGateway $varnishGateway)
    {
        $this->varnishGateway = $varnishGateway;
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        // No-op
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = array())
    {
        // No-op
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        // No-op
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->varnishGateway->invalidate(["all"]);
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key)
    {
        // No-op
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys)
    {
        // No-op
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item)
    {
        // No-op
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        // No-op
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        // No-op
    }
}
