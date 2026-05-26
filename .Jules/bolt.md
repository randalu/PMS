## 2024-05-26 - Setting Caching Fallback Edge Case
**Learning:** When caching single database record values (like settings) that might not exist and use a fallback, putting the fallback inside the cache closure caches the fallback value permanently if the DB row isn't present, breaking dynamic defaults.
**Action:** Evaluate the fallback outside the caching closure (`return $value ?? $default;`) so that only genuine database values are cached and empty states continue to fall back appropriately.
