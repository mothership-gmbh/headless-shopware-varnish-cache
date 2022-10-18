<?php
namespace Mothership\HeadlessShopwareVarnishCacheBundle\Cache;

interface GatewayInterface {
    public function getDecorated(): GatewayInterface;

    /**
     * Invalidates the cache by providing a list of tags
     *
     * @param string[] $tags
     *
     * @return void
     */
    public function invalidate(array $tags): void;

    /**
     * Bans multiple objects from cache via regex. This regex will be matched in Varnish VCL against urls in cache.
     *
     * @param string $regex
     *
     * @return void
     */
    public function ban(string $regex): void;

    /**
     * Purges a list of urls from Varnish cache. Please note: Purging urls is performanter than banning via methods
     * "ban" and "invalidate"
     *
     * @param string[] $urls
     *
     * @return void
     */
    public function purge(array $urls): void;
}
