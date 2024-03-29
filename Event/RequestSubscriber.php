<?php
declare(strict_types=1);

namespace Mothership\HeadlessShopwareVarnishCacheBundle\Event;

use Mothership\HeadlessShopwareVarnishCacheBundle\Cache\VarnishGateway;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;

class RequestSubscriber implements EventSubscriberInterface
{
    private VarnishGateway $varnishGateway;

    public function __construct(VarnishGateway $varnishGateway)
    {
        $this->varnishGateway = $varnishGateway;
    }

    /**
     * Subscribe to web controller and cli commands called in bin/console.
     *
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerStoppedEvent::class    => ['onWorkerStopped', 0],
            KernelEvents::FINISH_REQUEST => ['onKernelFinish', 0],
            ConsoleEvents::TERMINATE     => ['onConsoleTerminate', 0],
        ];
    }

    /**
     * Worker stops after 60s. See worker/supervisor configuration
     * Flush all collected tags in this case.
     *
     * @return void
     */
    public function onWorkerStopped(): void
    {
        $this->varnishGateway->flushCollected();
    }

    /**
     * When a request is finished, flush all collected invalidation tags, instead of flushing every tag on its own
     * @return void
     */
    public function onKernelFinish(): void
    {
        $this->varnishGateway->flushCollected();
    }

    /**
     * Same as for request finish, just for CLI commands.
     * @return void
     */
    public function onConsoleTerminate(): void
    {
        $this->varnishGateway->flushCollected();
    }
}
