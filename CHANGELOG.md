# 3.0.0
- Shopware 6.6 compatibility
- Added a docker-compose setup

# 2.0.1
- Fixes #6: The "all" tag invalidation when internal threshold is exceeded

# 2.0.0
- Shopware 6.5 compatibility
- removed custom cache pool and override of `CacheClearer` in favour of a new cache clearer using the tag `kernel.
  cache_clearer`. This is no change in functionality, just refactoring.
