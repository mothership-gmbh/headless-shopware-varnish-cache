Mothership Varnish cache adapter for headless Shopware
======================
This Shopware bundle adds a cache adapter for a varnish for headless shopware.  
If activated, it automatically invalidates a varnish cache when a cache-tag gets invalidated in Shopware. Like this,
an external frontend application can use an effective full page cache (FPC) together with a headless Shopware backend. 

This bundle is meant to be used together with
the [corresponding nuxt module](https://github.com/mothership-gmbh/nuxt-shopware-caching) e.g. for Shopware-PWA.
Nevertheless, it can be used without the nuxt module, if the headless application sets cache tags in another way.

This currently obviously only works on self-hosted Shopware instances, as you need to be able to install the bundle.

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
- `HEADLESS_VARNISH_HOSTS`: Comma-separated list of varnish hosts, e.g. "https://www.mydomain.de
  ,https://www.mydomain.com"

Example Varnish configuration
-----
See a stripped down example Varnish configuration [here](docs/example.vcl).  
Disclaimer: this is not a production ready configuration file and stripped down to show the essential parts for the
caching solution!

Usage
-----
After installation and activation the module automatically invalidates the varnish cache at the configured hosts when a 
cache-tag gets invalidated in Shopware.  
For example if a product changes, Shopware by default invalidates a cache-tag for this product. Like this all pages 
that contain this product get invalidated too and therefore are always up-to-date.

## CLI-Command to manually flush the cache

`bin/console varnish:invalidate`

- by default, it flushes the whole cache for all hosts
- --tags : Comma separated tags to clear
- --regex : Regex to match URLs which should be flushed

## Flush manually via curl

| Usecase          | Command                                                             | Description                                                |
|------------------|---------------------------------------------------------------------|------------------------------------------------------------|
| single URL       | `curl -X BAN https://www.domain.de/my/path`                         | Only the specified URL gets flushed                        |
| whole host       | `curl -X BAN -H 'X-Cache-Tags: all' https://www.domain.de`          | Flush all URLs on the specified host                       |
| single cache-tag | `curl -X BAN -H 'X-Cache-Tags: product-<ID>' https://www.domain.de` | Flush all sites with cache-tag "product-<ID>" on this host |

