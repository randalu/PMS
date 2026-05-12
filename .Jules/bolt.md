
## 2024-05-13 - Laravel Settings Caching
**Learning:** In Laravel applications, dynamically accessing global settings on each request creates N+1-style queries that are highly inefficient. `Cache::rememberForever` effectively eliminates redundant queries by storing values after the first read. However, caching must be paired with robust invalidation using Eloquent lifecycle events (`saved` and `deleted`) to prevent stale data.
**Action:** Always verify if database-backed application settings are cached. If not, implementing a `Cache::rememberForever` with appropriate model event-driven cache invalidation (`Cache::forget()`) is an easy win. Ensure explanatory comments accompany performance optimizations.
