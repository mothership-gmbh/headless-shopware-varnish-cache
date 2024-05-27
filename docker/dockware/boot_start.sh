#!/usr/bin/env bash

# See https://docs.dockware.io/development/custom-images
if ! grep -q "HeadlessShopwareVarnishCacheBundle" /var/www/html/composer.json
then
    echo "Add Bundle to composer autoloader. Not necessary if installed via 'composer require'."
    cat /var/www/html/composer.json | jq --indent 4 '.autoload."psr-4" += { "Mothership\\HeadlessShopwareVarnishCacheBundle\\": "src/HeadlessShopwareVarnishCacheBundle/src" }' | tee /var/www/html/composer.json
    composer dump-autoload
fi

if ! grep -q "HeadlessShopwareVarnishCacheBundle" /var/www/html/config/bundles.php
then
    echo "Add Bundle to bundles.php"
    sed -i '/];/i Mothership\\HeadlessShopwareVarnishCacheBundle\\HeadlessShopwareVarnishCacheBundle::class => ["all" => true],' /var/www/html/config/bundles.php
fi
