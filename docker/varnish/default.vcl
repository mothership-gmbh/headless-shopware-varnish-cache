vcl 4.0;

# Varnish VMODs, siehe https://varnish-cache.org/vmods/
import std;

# You should specify here all your app nodes and use round robin to select a backend
#include "vcl/config/backends.vcl";
backend default {
    .host = "shop";
    .port = "80";
}

# ACL for purgers IP. (This needs to contain app server ips)
#include "vcl/config/purge.vcl";
acl purgers {
    "127.0.0.1";
    "localhost";
    "::1";
    # Add other IPs here from which you want to be able to flush, especially your Shopware instance
    # if Varnish is on another host
}

sub vcl_recv {
    # Handle BAN
    if (req.method == "BAN") {
        if (!std.ip(req.http.X-Real-IP, "0.0.0.0") ~ purgers) {
            return (synth(405, "Method not allowed"));
        }

        if (!req.http.X-Url-Regex && !req.http.X-Cache-Tags) {
            # Purge direct url
            return (purge);
        }
        if (req.http.X-Cache-Tags == "all"){
            # Possibility to ban all
            ban("obj.http.X-Cache-Tags ~ .* && req.http.host == " + req.http.host);
            return (synth(200, "BAN URLs for all done."));
        }
        if (req.http.X-Cache-Tags) {
            # Logic for the ban, using the X-Cache-Tags header.
            ban("obj.http.X-Cache-Tags ~ " + req.http.X-Cache-Tags + " && req.http.host == " + req.http.host);
            return (synth(200, "BAN URLs containing cache tags (" + req.http.X-Cache-Tags + ") done."));
        }
        # Ban via Regex
        ban("req.url ~ " + req.http.X-Url-Regex + " && req.http.host == " + req.http.host);
        return (synth(200, "BAN URLs containing (" + req.http.X-Url-Regex + ") done."));
    }

    # We only deal with GET and HEAD by default
    if (req.method != "GET" && req.method != "HEAD") {
        return (pass);
    }

    # Always pass these paths directly to node without caching, add routes that should not be cached
    if (req.url ~ "^/(checkout|order-success|payment-failure|account|cart|store-api)(/.*)?$") {
        return (pass);
    }

    return (hash);
}

sub vcl_hash {
    hash_data(req.url);
    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }
    return (lookup);
}

sub vcl_hit {

}

sub vcl_backend_response {
    # Fix Vary Header in some cases
    # https://www.varnish-cache.org/trac/wiki/VCLExampleFixupVary
    if (beresp.http.Vary ~ "User-Agent") {
        set beresp.http.Vary = regsub(beresp.http.Vary, ",? *User-Agent *", "");
        set beresp.http.Vary = regsub(beresp.http.Vary, "^, *", "");
        if (beresp.http.Vary == "") {
            unset beresp.http.Vary;
        }
    }

    # Respect the Cache-Control=private header from the backend
    if (
        beresp.http.Pragma        ~ "no-cache" ||
        beresp.http.Cache-Control ~ "no-cache" ||
        beresp.http.Cache-Control ~ "private"
    ) {
        set beresp.ttl = 0s;
        set beresp.http.X-Cacheable = "NO:Cache-Control=private";
        set beresp.uncacheable = true;
        return (deliver);
    }

    # strip the cookie before the image is inserted into cache.
    if (bereq.url ~ "\.(png|gif|jpg|swf|css|js|webp)$") {
        unset beresp.http.set-cookie;
    }

    # Allow items to be stale if needed.
    set beresp.ttl = 24h;
    set beresp.grace = 6h;

    # Save the bereq.url so bans work efficiently
    set beresp.http.x-url = bereq.url;
    set beresp.http.X-Cacheable = "YES";

    return (deliver);
}

sub vcl_deliver {
    ## we don't want the client to cache
    set resp.http.Cache-Control = "max-age=0, private";

    # Set a cache header to allow us to inspect the response headers during testing
    if (obj.hits > 0) {
        unset resp.http.set-cookie;
        set resp.http.X-Cache = "HIT";
    }  else {
        set resp.http.X-Cache = "MISS";
    }

    # Cache Tags header should not be delivered as it is unneccessary for the client
    unset resp.http.X-Cache-Tags;
    unset resp.http.X-Cache-Tags-Number;

    set resp.http.X-Cache-Hits = obj.hits;
}
