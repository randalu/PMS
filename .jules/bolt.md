## 2026-05-19 - Settings Caching Pattern
**Learning:** Database queries for global application settings can execute multiple times per request if accessed in providers or middleware. Caching model queries requires evaluating default fallbacks outside the cache closure so defaults aren't incorrectly cached when no row exists.
**Action:** When caching model queries using `Cache::rememberForever`, extract default value logic outside the closure and ensure cache invalidation happens within `saved` and `deleted` model events.
