<?php

namespace Mothership\HeadlessShopwareVarnishCacheBundle\Adapter\Cache;

class CacheClearer extends \Shopware\Core\Framework\Adapter\Cache\CacheClearer
{
    /*
     * No-Op
     * Diese Überschreibung existiert nur, damit beim Debugging des Service-Containers die Überschreibung des
     * Services offensichtlicher ist und man schneller auf dieses Bundle und dadurch die Service-Definition stößt.
     *
     * In der Service-Definition wurde ein dritter Adapter als Teil des ersten Arguments übergeben.
     */
}
