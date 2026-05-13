## 2024-05-14 - Repeated Config Queries
**Learning:** StorefrontController fetching each configuration value dynamically via `Setting::getValue()` inside helper function `settings()` resulted in N+1 style query duplicates (6 repeated DB calls per storefront page load).
**Action:** Always wrap application-level global configuration DB lookups in a Cache::rememberForever block, and explicitly invalidate `settings.key` cache strings using model lifecycle hooks (`saved` and `deleted`) in the config model.
