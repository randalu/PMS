## 2024-05-15 - Caching Database Queries with Default Fallbacks
**Learning:** When caching database query results that have a default fallback, if you evaluate the fallback inside the caching closure, the cache will store the default value if no record exists. If the default value changes in the future code, the cache will still serve the old default value.
**Action:** Always evaluate the fallback outside the caching closure (e.g., `return $value ?? $default`) to prevent caching the default value when a database record does not exist.
