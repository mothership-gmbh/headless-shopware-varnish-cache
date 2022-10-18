Mothership Varnish cache adapter for headless Shopware
======================
This Shopware bundle adds a cache adapter for a varnish for headless shopware.
This bundle is meant to be used together with the [corresponding nuxt module](https://github.com/mothership-gmbh/nuxt-shopware-caching).
Nevertheless, it can be used without the nuxt module, if the headless application sets cache tags in another way.

Installation
------------
Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require mothership/headless-shopware-varnish-cache
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require mothership/headless-shopware-varnish-cache
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Mothership\HeadlessShopwareVarnishCacheBundle\HeadlessShopwareVarnishCacheBundle::class => ['all' => true],
];
```

#### Step 3: Configuration
First add the configuration file:
```yaml
# www/config/packages/cache.yml
headless_shopware_varnish_cache:
    enabled: "%env(bool:HEADLESS_VARNISH_ACTIVE)%"
    reverse_proxy:
        hosts: "%env(csv:HEADLESS_VARNISH_HOSTS)%"
        max_parallel_invalidations: 3
        ban_method: "BAN"
        tag_flush_threshold: 100
```

As you can see, two environment variables are used:
- `HEADLESS_VARNISH_ACTIVE`: 1 or 0 to activate/deactivate
- `HEADLESS_VARNISH_HOSTS`: Comma-separated list of varnish hosts, e.g. "https://www.mydomain.de,https://www.mydomain.com"

Varnish Configuration
-----
TODO: add example stripped down varnish config


Usage
-----

## CLI-Command to manually flush the cache
`bin/console varnish:invalidate`
- by default, it flushes the whole cache for all hosts
- --tags : Comma separated tags to clear
- --regex : Regex to match URLs which should be flushed


