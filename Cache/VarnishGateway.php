<?php
declare(strict_types=1);

namespace Mothership\HeadlessShopwareVarnishCacheBundle\Cache;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use RuntimeException;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;

class VarnishGateway implements GatewayInterface
{
    /**
     * @var string[]
     */
    private array $hosts;
    private int $concurrency;
    private string $banMethod;
    private bool $active;
    private array $invalidateTags = [];
    private array $invalidateRegex = [];
    private array $invalidateUrls = [];
    private int $tagFlushThreshold;
    private bool $useXKey;
    private int $xKeyChunkSize;

    public function __construct(bool $active, array $hosts, int $concurrency, string $banMethod, int $tagFlushThreshold, bool $useXKey, int $xKeyChunkSize)
    {
        $this->active = $active;
        $this->hosts = array_filter($hosts);
        $this->concurrency = $concurrency;
        $this->banMethod = $banMethod;
        $this->tagFlushThreshold = $tagFlushThreshold;
        $this->useXKey = $useXKey;
        $this->xKeyChunkSize = $xKeyChunkSize;
    }

    public function invalidate(array $tags): void
    {
        if (!$this->active) {
            return;
        }

        foreach ($tags as $tag) {
            $this->invalidateTags[$tag] = true;
        }
    }

    public function ban(string $regex): void
    {
        if (!$this->active) {
            return;
        }

        $this->invalidateRegex[$regex] = true;
    }

    public function purge(array $urls): void
    {
        if (!$this->active) {
            return;
        }

        foreach ($urls as $url) {
            $this->invalidateUrls[$url] = true;
        }
    }

    public function getDecorated(): GatewayInterface
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * This function is called when the original requests finishs its execution (via HttpRoute or console)
     *
     * @return void
     */
    public function flushCollected(): void
    {
        $requests = [];
        /**
         * When properties are updated via API, all products are purged. To avoid sending several thousand purge
         * requests, we send a purge all tag when a limit is reached.
         * @see \Shopware\Core\Framework\Adapter\Cache\CacheInvalidationSubscriber::getChangedPropertyFilterTags
         */
        if (count($this->invalidateTags) > $this->tagFlushThreshold) {
            $this->invalidateTags = ['all'];
            $this->invalidateRegex = [];
            $this->invalidateUrls = [];
        }

        foreach ($this->hosts as $host) {
            $tags = array_keys($this->invalidateTags);
            if(!empty($tags)){
                if ($this->useXKey) {
                    foreach (array_chunk($tags, $this->xKeyChunkSize) as $chunkedTags) {
                        $requests[] = new Request($this->banMethod, $host, ['xkey' => implode(' ', $chunkedTags)]);
                    }
                } else {
                    foreach ($tags as $tag) {
                        $requests[] = new Request($this->banMethod, $host, ['X-Cache-Tags' => $tag]);
                    }
                }
            }
            foreach ($this->invalidateRegex as $regex => $_) {
                $requests[] = new Request($this->banMethod, $host, ['X-Url-Regex' => $regex]);
            }
            foreach ($this->invalidateUrls as $url => $_) {
                $requests[] = new Request($this->banMethod, $host . $url);
            }
        }
        $this->invalidateTags = [];
        $this->invalidateRegex = [];
        $this->invalidateUrls = [];

        $pool = new Pool(new Client(), $requests, [
            'concurrency' => $this->concurrency,
            'reject'      => function (RequestException $reason): void {
                throw new RuntimeException(
                    sprintf(
                        'BAN request failed to %s failed with error: %s',
                        $reason->getRequest()->getUri()->__toString(),
                        $reason->getMessage()
                    )
                );
            },
        ]);

        $pool->promise()->wait();
    }
}
